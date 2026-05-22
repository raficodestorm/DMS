<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

test('verify password screen can be rendered', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/verify-email-password');

    $response->assertStatus(200);
});

test('user credentials can be verified', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password')
    ]);

    $response = $this->actingAs($user)->post('/verify-password', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('password.reset.form'));
    $this->assertTrue(session()->has('password_verified'));
});

test('invalid credentials are rejected in password verification', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password')
    ]);

    $response = $this->actingAs($user)->post('/verify-password', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors(['error']);
    $this->assertFalse(session()->has('password_verified'));
});

test('change password screen cannot be rendered if not verified', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/change-password');

    $response->assertRedirect(route('password.verify.form'));
});

test('change password screen can be rendered if verified', function () {
    $user = User::factory()->create();

    // Verify session
    $response = $this->actingAs($user)
        ->withSession(['password_verified' => true])
        ->get('/change-password');

    $response->assertStatus(200);
});

test('password can be changed if verified', function () {
    $user = User::factory()->create([
        'password' => Hash::make('old-password')
    ]);

    $response = $this->actingAs($user)
        ->withSession(['password_verified' => true])
        ->post('/change-password', [
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ]);

    $response->assertRedirect(route('dashboards'));
    $this->assertTrue(Hash::check('new-secure-password', $user->refresh()->password));
    $this->assertFalse(session()->has('password_verified'));
});
