<?php

namespace BenBjurstrom\Plink\Http\Controllers;

use BenBjurstrom\Plink\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;

class PostLoginController
{
    public function __invoke(LoginRequest $request): RedirectResponse
    {
        $user = $request->sendEmail();

        $url = URL::temporarySignedRoute(
            'plink.show', now()->addMinutes(5), ['id' => $user->getAuthIdentifier()]
        );

        return redirect($url);
    }
}
