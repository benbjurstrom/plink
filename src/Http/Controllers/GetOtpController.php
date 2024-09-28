<?php

namespace BenBjurstrom\Plink\Http\Controllers;

use BenBjurstrom\Plink\Enums\PlinkStatus;
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
            return redirect()->route('login')->withErrors(['email' => PlinkStatus::EXPIRED->errorMessage()])->withInput();
        }

        $model = Config::getAuthenticatableModel();
        $user = $model::findOrFail($id);

        $url = URL::temporarySignedRoute(
            'plink.post', now()->addMinutes(5), ['id' => $user->id]
        );

        return view('plink::plink', [
            'email' => $user->email,
            'url' => $url,
            'code' => $request->code,
        ]);
    }
}
