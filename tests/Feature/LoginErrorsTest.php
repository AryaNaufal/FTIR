<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginErrorsTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_signup_becomes_admin_and_following_signup_becomes_analyst(): void
    {
        $this->withoutVite();
        $this->get('/signup')->assertOk()->assertSee('Buat akun');

        $this->post('/signup', [
            'name' => 'Administrator Laboratorium',
            'email' => 'admin.laboratorium@example.test',
            'username' => 'adminlab',
            'password' => 'StrongPassword!123',
            'password_confirmation' => 'StrongPassword!123',
        ])->assertRedirect('/');

        $admin = User::where('username', 'adminlab')->firstOrFail();
        $this->assertSame('admin', $admin->role);
        $this->assertTrue((bool) $admin->active);
        $this->assertAuthenticatedAs($admin);

        auth()->logout();
        $this->post('/signup', [
            'name' => 'Analis QC',
            'email' => 'analis.qc@example.test',
            'username' => 'analisqc',
            'password' => 'StrongPassword!123',
            'password_confirmation' => 'StrongPassword!123',
        ])->assertRedirect('/');

        $this->assertDatabaseHas('users', ['username' => 'analisqc', 'role' => 'analis', 'active' => true]);
    }

    public function test_incorrect_credentials_show_only_credentials_error(): void
    {
        $this->withoutVite();
        app()->setLocale('id');

        $this->from('/login')->post('/login', ['email' => 'unknown', 'password' => 'WrongPassword!123'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['email' => 'Email/username atau kata sandi tidak sesuai.']);

        $this->get('/login')->assertOk()
            ->assertSee('Email/username atau kata sandi tidak sesuai.')
            ->assertDontSee('Terlalu banyak percobaan')
            ->assertDontSee('WrongPassword!123');
    }

    public function test_throttled_login_shows_wait_time_and_recovers_after_expiry(): void
    {
        $this->withoutVite();
        app()->setLocale('id');
        $this->freezeTime();
        $user = User::factory()->create(['password' => bcrypt('CorrectPassword!123')]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->from('/login')->post('/login', ['email' => $user->email, 'password' => 'WrongPassword!123'])
                ->assertSessionHasErrors(['email' => __('auth.failed')]);
        }

        $this->post('/login', ['email' => $user->email, 'password' => 'CorrectPassword!123'])
            ->assertRedirect('/login')
            ->assertHeader('Retry-After', '60')
            ->assertSessionHasErrors(['email' => 'Terlalu banyak percobaan masuk. Coba lagi dalam 60 detik.'])
            ->assertSessionHas('_old_input.email', $user->email)
            ->assertSessionMissing('_old_input.password');

        $this->get('/login')->assertOk()
            ->assertSee('Terlalu banyak percobaan masuk. Coba lagi dalam 60 detik.')
            ->assertDontSee('Email/username atau kata sandi tidak sesuai.');

        $this->postJson('/login', ['email' => $user->email, 'password' => 'CorrectPassword!123'])
            ->assertStatus(429)->assertHeader('Retry-After', '60')
            ->assertJsonPath('message', 'Terlalu banyak percobaan masuk. Coba lagi dalam 60 detik.');

        $this->travel(61)->seconds();
        $this->post('/login', ['email' => $user->email, 'password' => 'CorrectPassword!123'])->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }
}
