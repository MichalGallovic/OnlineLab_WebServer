<?php

namespace App\Http\Middleware;

use Closure;
use Modules\Controller\Entities\Regulator;

class ControllerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $regulator = Regulator::findOrFail($request->controllerId);

        if ($regulator->type == 'public' || $regulator->user->id == $request->user()->user->id)
        {
            return $next($request);
        }

        return redirect('controller');
    }
}
