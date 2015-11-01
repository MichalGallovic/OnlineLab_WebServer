<?php namespace Modules\Localization\Http\Middleware; 

use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;
class Language {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Session::has('applocale') AND array_key_exists(Session::get('applocale'), config('localization.langs'))) {
            App::setLocale(Session::get('applocale'));
        }

    	return $next($request);
    }
    
}
