<?php

namespace BenBjurstrom\Plink\Tests\Feature\Http\Controllers;

use BenBjurstrom\Plink\Actions\CreatePlink;
use BenBjurstrom\Plink\Enums\PlinkStatus;
use BenBjurstrom\Plink\Tests\Support\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

beforeEach(function () {
    Route::get('login', fn()=> 'login')
        ->name('login')
        ->middleware('guest');

    $this->user = User::factory()->create();
    $this->plink = (new CreatePlink)->handle($this->user);
});
function getSignedUrl($plink, $exipration = 5, $session = null)
{
    return URL::temporarySignedRoute('plink.show', $exipration,
        [
            'id' => $plink->id,
            'session' => $session ?? session()->getId(),
        ]
    );
}

it('redirects to dashboard with valid signature and non-expired plink', function () {
    $url = getSignedUrl($this->plink);

    $response = $this->get($url);

    $response->assertRedirect('/dashboard');
    expect($this->user->fresh()->hasVerifiedEmail())->toBeTrue();
});

it('shows error page when signature expires', function () {
    $this->signedUrl = URL::temporarySignedRoute('plink.show', 1,
        [
            'id' => $this->plink->id,
            'session' => session()->getId(),
        ]
    );

    // Travel past expiration time
    $this->travel(3)->minutes();

    $response = $this->get($this->signedUrl);

    $response->assertOk()
        ->assertViewIs('plink::error')
        ->assertSee(PlinkStatus::INVALID_EXPIRED->errorMessage());
});

it('respects custom expiration time from config', function () {
    // Set custom expiration time
    Config::set('plink.expiration', 4); // 4 minutes

    // Travel 4 minutes into the future (should still work)
    $this->travel(3)->minutes();
    $response = $this->get($this->signedUrl);
    $response->assertRedirect('/dashboard');

    // Then sign out
    Auth::logout();

    // Travel 2 more minutes (now expired)
    $this->travel(2)->minutes();
    $response = $this->get($this->signedUrl);
    $response->assertViewIs('plink::error')
        ->assertSee(PlinkStatus::INVALID_EXPIRED->errorMessage());
});

it('shows error for invalid signature', function () {
    // Tamper with the signature by adding a character
    $tamperedUrl = $this->signedUrl . 'x';

    $response = $this->get($tamperedUrl);

    $response->assertOk()
        ->assertViewIs('plink::error')
        ->assertSee(PlinkStatus::INVALID->errorMessage());
});

it('shows error when plink is already used', function () {
    // Use the plink once
    $this->get($this->signedUrl);

    // Then sign out
    Auth::logout();

    // Try to use it again
    $response = $this->get($this->signedUrl);

    $response->assertOk()
        ->assertViewIs('plink::error')
        ->assertSee(PlinkStatus::USED->errorMessage());
});

it('shows error when session does not match', function () {
    // Generate URL with different session ID
    $differentSessionUrl = URL::temporarySignedRoute(
        'plink.show',
        now()->addMinutes(config('plink.expiration', 60)),
        [
            'id' => $this->plink->id,
            'session' => 'different-session-id',
        ]
    );

    $response = $this->get($differentSessionUrl);

    $response->assertOk()
        ->assertViewIs('plink::error')
        ->assertSee(PlinkStatus::SESSION->errorMessage());
});

it('verifies unverified email upon successful login', function () {
    $this->user->email_verified_at = null;
    $this->user->save();

    expect($this->user->fresh()->hasVerifiedEmail())->toBeFalse();

    $response = $this->get($this->signedUrl);

    $response->assertRedirect('/dashboard');
    expect($this->user->fresh()->hasVerifiedEmail())->toBeTrue();
});
