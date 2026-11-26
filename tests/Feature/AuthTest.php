<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'Rafli Nasution',
            'email'                 => 'rafli@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'rafli@example.com']);
    }

    public function test_user_cannot_register_with_existing_email(): void
    {
        User::factory()->create(['email' => 'rafli@example.com']);

        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'User Lain',
            'email'                 => 'rafli@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_login_and_get_token(): void
    {
        User::factory()->create([
            'email'    => 'rafli@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email'    => 'rafli@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        User::factory()->create([
            'email'    => 'rafli@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email'    => 'rafli@example.com',
            'password' => 'salah123',
        ]);

        $response->assertStatus(401); // Ubah ke 422 jika controller login melempar ValidationException
    }
}