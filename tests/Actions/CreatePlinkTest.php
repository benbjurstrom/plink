<?php

use BenBjurstrom\Plink\Actions\CreatePlink;
use BenBjurstrom\Plink\Enums\PlinkStatus;
use BenBjurstrom\Plink\Exceptions\PlinkThrottleException;
use BenBjurstrom\Plink\Tests\Support\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Mock the request IP address
    Request::macro('ip', fn () => '127.0.0.1');
});

it('creates a new plink for a user', function () {
    $user = User::factory()->create();

    $plink = (new CreatePlink)->handle($user);

    expect($plink)
        ->status->toBe(PlinkStatus::ACTIVE)
        ->ip_address->toBe('127.0.0.1')
        ->user_id->toBe($user->id);
});

it('supersedes existing active plinks when creating a new one', function () {
    $user = User::factory()->create();

    // Create an initial active plink
    $this->travel(-2)->minutes();
    $firstPlink = (new CreatePlink)->handle($user);

    // Create a second plink
    $this->travelBack();
    $secondPlink = (new CreatePlink)->handle($user);

    // Refresh the first plink from database
    $firstPlink->refresh();

    expect($firstPlink->status)->toBe(PlinkStatus::SUPERSEDED)
        ->and($secondPlink->status)->toBe(PlinkStatus::ACTIVE);
});

it('throws throttle exception when exceeding 1 plink per minute', function () {
    $user = User::factory()->create();

    // Create first plink
    (new CreatePlink)->handle($user);

    // Attempt to create second plink within a minute
    (new CreatePlink)->handle($user);
})->throws(PlinkThrottleException::class);

it('throws throttle exception when exceeding 3 plinks per 5 minutes', function () {
    $user = User::factory()->create();

    // Create 3 plinks with timestamps 2 minutes apart
    for ($i = 0; $i < 3; $i++) {
        $this->travel(-4 + ($i * 2))->minutes();
        (new CreatePlink)->handle($user);
    }

    // Attempt to create fourth plink within 5 minutes of first
    (new CreatePlink)->handle($user);
})->throws(PlinkThrottleException::class);

it('throws throttle exception when exceeding 5 plinks per 30 minutes', function () {
    $user = User::factory()->create();

    // Create 5 plinks with timestamps 6 minutes apart
    for ($i = 0; $i < 5; $i++) {
        $this->travel(-24 + ($i * 6))->minutes();
        (new CreatePlink)->handle($user);
    }

    // Attempt to create sixth plink within 30 minutes of first
    (new CreatePlink)->handle($user);
})->throws(PlinkThrottleException::class);

it('allows creating new plink after throttle period expires', function () {
    $user = User::factory()->create();

    // Create initial plink
    $this->travel(-2)->minutes();
    (new CreatePlink)->handle($user);

    // Travel past the 1-minute throttle period
    $this->travelBack();

    // Should be able to create new plink
    $newPlink = (new CreatePlink)->handle($user);

    expect($newPlink)
        ->status->toBe(PlinkStatus::ACTIVE)
        ->ip_address->toBe('127.0.0.1');
});

it('only counts non-used plinks for throttling', function () {
    $user = User::factory()->create();

    // Create a plink and mark it as used
    $this->travel(-30)->seconds();
    $usedPlink = (new CreatePlink)->handle($user);
    $usedPlink->update(['status' => PlinkStatus::USED]);

    // Should be able to create new plink immediately
    $newPlink = (new CreatePlink)->handle($user);

    expect($newPlink)
        ->status->toBe(PlinkStatus::ACTIVE)
        ->ip_address->toBe('127.0.0.1');
});

it('respects custom threshold values from config', function () {
    $user = User::factory()->create();

    // Override the default thresholds
    Config::set('plink.limits', [
        ['limit' => 2, 'minutes' => 2],  // Allow 2 plinks within 2 minutes
        ['limit' => 4, 'minutes' => 10], // Allow 4 plinks within 10 minutes
    ]);

    // Should allow 2 plinks within 2 minutes
    $this->travel(-1)->minutes();
    $firstPlink = (new CreatePlink)->handle($user);
    $secondPlink = (new CreatePlink)->handle($user);

    expect($firstPlink->refresh()->status)->toBe(PlinkStatus::SUPERSEDED)
        ->and($secondPlink->status)->toBe(PlinkStatus::ACTIVE);

    // Third attempt within 2 minutes should throw exception
    (new CreatePlink)->handle($user);
})->throws(PlinkThrottleException::class, 'Too many links requested. Please wait 1 minutes and 59 seconds before trying again.');

it('allows plinks after custom threshold period expires', function () {
    $user = User::factory()->create();

    // Set a single threshold of 2 plinks per 3 minutes
    Config::set('plink.limits', [
        ['limit' => 2, 'minutes' => 3],
    ]);

    // Create 2 plinks
    $this->travel(-2)->minutes();
    (new CreatePlink)->handle($user);
    (new CreatePlink)->handle($user);

    // Attempt third plink within threshold period should fail
    $this->travel(1)->minutes();
    expect(fn () => (new CreatePlink)->handle($user))
        ->toThrow(PlinkThrottleException::class);

    // After threshold period expires, should allow new plink
    $this->travel(3)->minutes();
    $newPlink = (new CreatePlink)->handle($user);

    expect($newPlink)
        ->status->toBe(PlinkStatus::ACTIVE)
        ->ip_address->toBe('127.0.0.1');
});
