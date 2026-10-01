<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_registration_pages_are_available(): void
    {
        $this->get(route('login'))->assertOk()->assertSee('Giriş yap')->assertSee('E-posta adresi');
        $this->get(route('register'))->assertOk()->assertSee('Kayıt ol')->assertSee('Şifreyi onayla');
    }

    public function test_registration_requires_matching_passwords_and_does_not_create_user_on_mismatch(): void
    {
        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Ayşe Yılmaz',
                'email' => 'ayse@example.com',
                'phone' => '05001234567',
                'password' => 'StrongPass123',
                'password_confirmation' => 'DifferentPass123',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('password');

        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_registration_saves_user_hashes_password_and_logs_user_in(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Ayşe Yılmaz',
            'email' => 'ayse@example.com',
            'phone' => '05001234567',
            'password' => 'StrongPass123',
            'password_confirmation' => 'StrongPass123',
        ])->assertRedirect(route('home'));

        $user = User::where('email', 'ayse@example.com')->firstOrFail();

        $this->assertSame('Ayşe Yılmaz', $user->name);
        $this->assertSame('05001234567', $user->phone);
        $this->assertNotSame('StrongPass123', $user->password);
        $this->assertTrue(Hash::check('StrongPass123', $user->password));
        $this->assertAuthenticatedAs($user);
    }

    public function test_existing_user_can_log_in_with_correct_email_and_password(): void
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'password' => 'StrongPass123',
        ]);

        $this->post(route('login.store'), [
            'email' => 'existing@example.com',
            'password' => 'StrongPass123',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_rejects_an_unknown_email_or_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'existing@example.com',
            'password' => 'StrongPass123',
        ]);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => 'existing@example.com',
                'password' => 'WrongPass123',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_authenticated_user_can_log_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }
}
