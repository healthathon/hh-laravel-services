<?php
// @author JEET DUMS
namespace App\Http\Middleware;

use Closure;

class AdminRoutesMiddleware
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


        if (auth()->guard('admin')->guest())
        {
            return redirect()->route('admin.loginForm');
        }else {
            return $next($request);
        }
    }
}
