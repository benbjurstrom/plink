<?php

namespace BenBjurstrom\Otpz\Http\Controllers;

use BenBjurstrom\Otpz\Actions\AttemptOtp;
use BenBjurstrom\Otpz\Enums\OtpStatus;
use BenBjurstrom\Otpz\Support\Config;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostOtpLinkController
{
    public function __invoke(Request $request): RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            return redirect()->route('login')->withErrors(['email' => OtpStatus::EXPIRED->errorMessage()])->withInput();
        }

        $code = request()->query('code');
        $email = request()->query('email');

        $model = Config::getAuthenticatableModel();
        $user = $model::where('email', $email)->firstOrfail();

        try {
            (new AttemptOtp)->handle($user, $code);
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => $e->getMessage()])->withInput();
        }

        Auth::loginUsingId($user->id/*$this->remember*/);
        session()->regenerate();

        return redirect()->intended('/dashboard');
    }
}
