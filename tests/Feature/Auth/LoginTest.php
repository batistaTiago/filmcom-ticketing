<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /** @test */
    public function should_return_a_token_along_with_its_expiration_date()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'password'
        ])->assertOk()->assertJsonStructure([
            'token',
            'expires_at'
        ]);
    }

    /** @test */
    public function should_return_unauthorized_if_user_is_not_found()
    {
        $this->postJson(route('auth.login'), [
            'email' => fake()->email,
            'password' => 'password'
        ])->assertUnauthorized();
    }

    /** @test */
    public function should_return_unauthorized_if_passwords_dont_match()
    {
        $user = User::factory()->create([
            'password' => 'user_password'
        ]);

        $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'password'
        ])->assertUnauthorized();
    }
}
