<?php

namespace App\Http\Middleware;

use Closure;

class Admin
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
        if ($request->user() && $request->user()->roles_id != 2) {
            return response()->json(array('error_type' => 'unauthorized', 'code' => 401, 'error_message' => 'You dont have permission to proceede!'), 401);
        }
        return $next($request);
    }
}
