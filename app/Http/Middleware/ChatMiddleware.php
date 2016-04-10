<?php

namespace App\Http\Middleware;

use Closure;

class ChatMiddleware
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

        if ($request->user()->user->canAccessChatroom(intval($request->chatroom)))
        {
            return $next($request);
        }

        return redirect('chat');
    }
}
