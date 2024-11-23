<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use JWTAuth, Lang;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    use ResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            DB::table('users')->where('id',$user->id)->update(['last_login' => Carbon::now()]);
            if (!$user) {
                return $this->sendFailedResponse(Lang::get('messages.user.auth_token_not_found'), 401);
            }
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return $this->sendFailedResponse(Lang::get('messages.user.token_invalid'), 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return $this->sendFailedResponse(Lang::get('messages.user.token_expired'), 401);
            } else {
                return $this->sendFailedResponse(Lang::get('messages.user.auth_token_not_found'), 401);
            }
        }
        return $next($request);
    }
}
