<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
 public function handle($request, Closure $next)
{
    $locale = session('locale', config('app.locale'));
    
    \Log::info('Current Locale: ' . $locale); // Add this to check logs

    App::setLocale($locale);

    return $next($request);
}

}

