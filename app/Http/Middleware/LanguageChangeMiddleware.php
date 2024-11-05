<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LanguageChangeMiddleware
{
    /**
     * use Illuminate\Support\Facades\Session;
     */
    public function handle(Request $request, Closure $next): Response
    {
        $localeLanguage = \Session::get('languageChangeName');
        $defaultLanguage = getSuperAdminSettingValue()['default_language']->value;

        if (! isset($localeLanguage)) {
            if(!empty($defaultLanguage)){
                \App::setLocale($defaultLanguage);
                $defaultLanguage;
            }else{
                \App::setLocale('en');
            }
        } else {
            \App::setLocale($localeLanguage);
        }

        return $next($request);
    }
}
