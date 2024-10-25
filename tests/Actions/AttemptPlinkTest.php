<?php

use BenBjurstrom\Plink\Actions\AttemptPlink;
use BenBjurstrom\Plink\Enums\PlinkStatus;
use BenBjurstrom\Plink\Exceptions\PlinkAttemptException;
use BenBjurstrom\Plink\Models\Plink;
use BenBjurstrom\Plink\Tests\Support\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Mock session ID
    Session::shouldReceive('getId')->andReturn('test-session-id');

    // Set up request with valid signature by default
    Request::macro('hasValidSignature', fn () => true);
});

it('successfully validates and marks plink as used', function () {
    $user = User::factory()->create();
    $plink = Plink::factory()
        ->for($user)
        ->create();

    Request::merge(['session' => 'test-session-id']);

    $attemptedPlink = (new AttemptPlink)->handle($plink->id);

    expect($attemptedPlink->refresh())
        ->status->toBe(PlinkStatus::USED)
        ->id->toBe($plink->id);
});

it('throws exception for invalid signature', function () {
    $plink = Plink::factory()->create();
    Request::macro('hasValidSignature', fn () => false);

    expect(fn () => (new AttemptPlink)->handle($plink->id))
        ->toThrow(PlinkAttemptException::class, PlinkStatus::INVALID->errorMessage());
});

it('throws exception for expired signature', function () {
    $plink = Plink::factory()->create();
    Request::macro('hasValidSignature', fn () => false);
    URL::macro('signatureHasNotExpired', fn () => false);

    expect(fn () => (new AttemptPlink)->handle($plink->id))
        ->toThrow(PlinkAttemptException::class, PlinkStatus::EXPIRED->errorMessage());
});

it('throws exception for non-active plink status', function () {
    $plink = Plink::factory()
        ->used()
        ->create();

    expect(fn () => (new AttemptPlink)->handle($plink->id))
        ->toThrow(PlinkAttemptException::class, PlinkStatus::USED->errorMessage());
});

it('throws exception for expired plink (older than 5 minutes)', function () {
    $plink = Plink::factory()
        ->expired()
        ->create();

    expect(fn () => (new AttemptPlink)->handle($plink->id))
        ->toThrow(PlinkAttemptException::class, PlinkStatus::EXPIRED->errorMessage());

    expect($plink->refresh()->status)->toBe(PlinkStatus::EXPIRED);
});

it('throws exception for invalid session', function () {
    $plink = Plink::factory()->create();
    Request::merge(['session' => 'wrong-session-id']);

    expect(fn () => (new AttemptPlink)->handle($plink->id))
        ->toThrow(PlinkAttemptException::class, PlinkStatus::SESSION->errorMessage());
});

it('throws exception for non-existent plink', function () {
    expect(fn () => (new AttemptPlink)->handle(999))
        ->toThrow(Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

it('allows attempt within 5 minute window', function () {
    $plink = Plink::factory()
        ->state(['created_at' => now()->subMinutes(4)])
        ->create();

    Request::merge(['session' => 'test-session-id']);

    $attemptedPlink = (new AttemptPlink)->handle($plink->id);

    expect($attemptedPlink->refresh())
        ->status->toBe(PlinkStatus::USED);
});
