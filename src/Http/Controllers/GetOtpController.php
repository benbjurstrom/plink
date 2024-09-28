<?php

namespace BenBjurstrom\Plink\Http\Controllers;

use BenBjurstrom\Plink\Enums\OtpStatus;
use BenBjurstrom\Plink\Support\Config;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class GetOtpController
{
    public function __invoke(Request $request, int $id): View|RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            return redirect()->route('login')->withErrors(['email' => OtpStatus::EXPIRED->errorMessage()])->withInput();
        }

        $model = Config::getAuthenticatableModel();
        $user = $model::findOrFail($id);

        $url = URL::temporarySignedRoute(
            'otp.post', now()->addMinutes(5), ['id' => $user->id]
        );

        return view('plink::otp', [
            'email' => $user->email,
            'url' => $url,
            'code' => $request->code,
        ]);
    }
}
