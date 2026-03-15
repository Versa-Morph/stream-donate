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
            'otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]+$/'],
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size'     => 'Kode OTP harus 6 digit.',
            'otp.regex'    => 'Kode OTP hanya berisi angka.',
        ]);

        $data = $request->session()->get('otp_register');

        if (! $data) {
            return redirect()->route('login')->with('status', 'Sesi pendaftaran kadaluarsa. Silakan daftar ulang.');
        }

        $record = OtpCode::where('email', $data['email'])
            ->where('code', $request->otp)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $record) {
            return back()->withErrors(['otp' => 'Kode OTP salah atau sudah kadaluarsa.'])->withInput();
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
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'email'      => $email,
            'code'       => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($email)->queue(new OtpMail($code, $name));

        return $code;
    }
}
