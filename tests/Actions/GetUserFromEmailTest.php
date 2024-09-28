<?php

use BenBjurstrom\Plink\Actions\GetUserFromEmail;
use BenBjurstrom\Plink\Tests\Support\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('retrieves an existing user by email', function () {
    $randomEmail = 'test_'.Str::random(10).'@example.com';
    $existingUser = User::factory()->create(['email' => $randomEmail]);

    $user = (new GetUserFromEmail)->handle($randomEmail);

    expect($user->id)->toBe($existingUser->id);
});

it('creates a new user if email does not exist', function () {
    $randomEmail = 'newuser_'.Str::random(10).'@example.com';
    $user = (new GetUserFromEmail)->handle($randomEmail);

    expect($user->email)->toBe($randomEmail);
    expect(Str::length($user->password))->toBe(32);
    expect($user->name)->toBe('');
    expect($user->exists)->toBeTrue();

    // Verify the user is in the database
    $this->assertDatabaseHas('users', [
        'email' => $randomEmail,
    ]);
});
