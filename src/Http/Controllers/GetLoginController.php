<?php

namespace BenBjurstrom\Plink\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class GetLoginController
{
    public function __invoke(Request $request): View
    {
        return view('plink::login');
    }
}
