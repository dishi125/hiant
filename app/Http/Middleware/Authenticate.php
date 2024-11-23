<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\EntityTypes;

class Authenticate extends Middleware
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
        if (!Auth::check()){
            return redirect('login');
        }
        
        $allRoles = Auth::user()->all_entity_type_id;
        if(!in_array(EntityTypes::NORMALUSER, $allRoles) && !in_array(EntityTypes::ADMIN, $allRoles) && !in_array(EntityTypes::MANAGER, $allRoles) && !in_array(EntityTypes::SUBMANAGER, $allRoles)){
            Auth::guard()->logout();
            $request->session()->invalidate();
            return redirect('login');
        }
        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        
        if (! $request->expectsJson()) {
            return route('login');
        }
    }
}
