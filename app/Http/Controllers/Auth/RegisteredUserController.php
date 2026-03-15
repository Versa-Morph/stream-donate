<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     * Sekarang redirect ke halaman login (tab register).
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('login')->with('tab', 'register');
    }

    /**
     * Handle an incoming registration request.
     * Data disimpan sementara di session, OTP dikirim ke email.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Simpan data registrasi di session (belum buat user)
        $request->session()->put('otp_register', [
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Kirim OTP
        OtpController::sendOtp($request->email, $request->name);

        return redirect()->route('otp.show');
    }
}
