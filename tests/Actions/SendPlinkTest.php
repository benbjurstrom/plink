<?php

use BenBjurstrom\Plink\Actions\SendPlink;
use BenBjurstrom\Plink\Mail\PlinkMail;
use BenBjurstrom\Plink\Tests\Support\CustomPlinkMail;
use BenBjurstrom\Plink\Tests\Support\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
});

it('sends a plink email to an existing user', function () {
    $randomEmail = 'test_'.Str::random(10).'@example.com';
    $existingUser = User::factory()->create(['email' => $randomEmail]);

    $user = (new SendPlink)->handle($randomEmail);

    expect($user->id)->toBe($existingUser->id);

    Mail::assertSent(PlinkMail::class, function ($mail) use ($existingUser) {
        return $mail->hasTo($existingUser->email);
    });
});

it('creates a new user and sends them a plink email', function () {
    $randomEmail = 'newuser_'.Str::random(10).'@example.com';

    $user = (new SendPlink)->handle($randomEmail);

    expect($user->email)->toBe($randomEmail);
    expect($user->exists)->toBeTrue();

    Mail::assertSent(PlinkMail::class, function ($mail) use ($randomEmail) {
        return $mail->hasTo($randomEmail);
    });
});

it('uses the configured mailable class', function () {
    // Create a test mailable class
    config(['plink.mailable' => CustomPlinkMail::class]);

    $randomEmail = 'test_'.Str::random(10).'@example.com';

    (new SendPlink)->handle($randomEmail);

    Mail::assertSent(CustomPlinkMail::class);
    Mail::assertNotSent(PlinkMail::class);
});

