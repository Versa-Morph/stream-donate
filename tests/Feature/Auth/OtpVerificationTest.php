<?php

namespace Tests\Feature\Auth;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OtpVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Drive the real /register endpoint to reach the "OTP sent, awaiting
     * verification" state, then read back the actual generated code from the
     * DB (it's only ever emailed in production, never returned in the HTTP
     * response) so the test can submit it like a real donor would.
     */
    private function registerAndGetOtpCode(string $email = 'test@example.com'): string
    {
        Mail::fake();

        $this->post('/register', [
            'name' => 'Test User',
            'email' => $email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        return OtpCode::where('email', $email)->latest()->first()->code;
    }

    public function test_correct_otp_creates_user_and_logs_them_in(): void
    {
        $code = $this->registerAndGetOtpCode();

        $response = $this->post('/otp/verify', ['otp' => $code]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));

        $user = User::where('email', 'test@example.com')->firstOrFail();
        $this->assertNotNull($user->email_verified_at);

        $otp = OtpCode::where('email', 'test@example.com')->firstOrFail();
        $this->assertNotNull($otp->used_at);
    }

    public function test_incorrect_otp_does_not_create_user_or_authenticate(): void
    {
        $this->registerAndGetOtpCode();

        $response = $this->post('/otp/verify', ['otp' => 'WRONGCOD']);

        $response->assertSessionHasErrors('otp');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);

        $otp = OtpCode::where('email', 'test@example.com')->firstOrFail();
        $this->assertSame(1, $otp->attempt_count);
    }
}
