<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OtpController extends Controller
{
    /**
     * Tampilkan halaman input OTP.
     */
    public function show(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('otp_register')) {
            return redirect()->route('login')->with('status', 'Sesi pendaftaran tidak ditemukan. Silakan daftar ulang.');
        }

        return view('auth.verify-otp');
    }

    /**
     * Verifikasi kode OTP yang dimasukkan user.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:8', 'regex:/^[A-Z0-9]+$/'],
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size'     => 'Kode OTP harus 8 karakter.',
            'otp.regex'    => 'Kode OTP hanya berisi huruf kapital dan angka.',
        ]);

        $data = $request->session()->get('otp_register');

        if (! $data) {
            return redirect()->route('login')->with('status', 'Sesi pendaftaran kadaluarsa. Silakan daftar ulang.');
        }

        // Check rate limiting: max 5 verification attempts per hour per email
        $recentAttempts = OtpCode::where('email', $data['email'])
            ->where('created_at', '>', now()->subHour())
            ->sum('attempt_count');

        if ($recentAttempts >= 5) {
            return back()->withErrors(['otp' => 'Terlalu banyak percobaan. Coba lagi dalam 1 jam.'])->withInput();
        }

        $record = OtpCode::where('email', $data['email'])
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $record) {
            return back()->withErrors(['otp' => 'Kode OTP tidak ditemukan atau sudah kadaluarsa.'])->withInput();
        }

        // Check if OTP is locked due to failed attempts
        if ($record->isLocked()) {
            return back()->withErrors(['otp' => 'Kode OTP terkunci karena terlalu banyak percobaan gagal. Minta kode baru.'])->withInput();
        }

        // Verify OTP code
        if ($record->code !== strtoupper($request->otp)) {
            $record->incrementAttempts();
            return back()->withErrors(['otp' => 'Kode OTP salah. Percobaan tersisa: ' . (3 - $record->attempt_count)])->withInput();
        }

        // Tandai OTP sudah dipakai
        $record->update(['used_at' => now()]);

        // Buat user baru
        $user = User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'password'          => $data['password'], // sudah di-hash
            'email_verified_at' => now(), // langsung verified karena OTP
        ]);

        // Hapus data sesi OTP
        $request->session()->forget('otp_register');

        // Login otomatis
        Auth::login($user);

        return redirect()->route('dashboard');
    }

    /**
     * Kirim ulang OTP ke email.
     */
    public function resend(Request $request): RedirectResponse
    {
        $data = $request->session()->get('otp_register');

        if (! $data) {
            return redirect()->route('login')->with('status', 'Sesi tidak ditemukan. Silakan daftar ulang.');
        }

        // Cek apakah ada OTP yang masih berlaku (throttle: minimal 60 detik)
        $recent = OtpCode::where('email', $data['email'])
            ->where('created_at', '>', now()->subSeconds(60))
            ->exists();

        if ($recent) {
            return back()->withErrors(['otp' => 'Tunggu 60 detik sebelum meminta kode baru.']);
        }

        $this->sendOtp($data['email'], $data['name']);

        return back()->with('status', 'Kode OTP baru telah dikirim ke email kamu.');
    }

    /**
     * Generate kode OTP, simpan di DB, dan kirim email.
     */
    public static function sendOtp(string $email, string $name): string
    {
        // Generate 8-character alphanumeric code (uppercase letters and numbers only)
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Exclude similar chars: I,O,0,1
        $code = '';
        for ($i = 0; $i < 8; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }

        OtpCode::create([
            'email'      => $email,
            'code'       => $code,
            'expires_at' => now()->addMinutes(5), // Reduced from 10 to 5 minutes
        ]);

        Mail::to($email)->queue(new OtpMail($code, $name));

        return $code;
    }
}
