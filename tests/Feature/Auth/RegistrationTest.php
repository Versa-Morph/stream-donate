<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_redirects_to_login_register_tab(): void
    {
        // RegisteredUserController::create() deliberately redirects to the
        // combined login/register page instead of rendering its own view.
        $response = $this->get('/register');

        $response->assertRedirect(route('login'));
    }

    public function test_submitting_registration_stores_pending_data_and_redirects_to_otp(): void
    {
        Mail::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // No user is created and nobody is logged in yet — registration only
        // completes once the OTP is verified (see OtpVerificationTest).
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
        $response->assertRedirect(route('otp.show'));
        $response->assertSessionHas('otp_register.email', 'test@example.com');
    }
}
