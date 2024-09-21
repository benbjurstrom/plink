<?php

namespace BenBjurstrom\Otpz\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class GetLoginController
{
    public function __invoke(Request $request): View
    {
        return view('otpz::login');
    }
}
