<?php

namespace BenBjurstrom\Plink\Http\Controllers;

use BenBjurstrom\Plink\Enums\OtpStatus;
use BenBjurstrom\Plink\Http\Requests\OtpRequest;
use BenBjurstrom\Plink\Support\Config;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class PostOtpController
{
    public function __invoke(OtpRequest $request, int $id): RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            return redirect()->route('login')->withErrors(['email' => OtpStatus::EXPIRED->errorMessage()])->withInput();
        }

        $model = Config::getAuthenticatableModel();
        $user = $model::findOrFail($id);
        $request->authenticate($user);

        Session::regenerate();

        return redirect()->intended('/dashboard');
    }
}
