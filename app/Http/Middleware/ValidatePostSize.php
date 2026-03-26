<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\ValidatePostSize as Middleware;

class ValidatePostSize extends Middleware
{
    protected function go($request, $next)
    {
        return parent::handle($request, $next);
    }
}
