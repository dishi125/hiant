<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Config;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use Log;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Models\EntityTypes;
use App\Models\UserDetail;
use App\Models\Cards;
use Hash;
use DB;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $expireMasterPassword = Config::expirePassword();
    }

    protected function authenticated(Request $request, $user)
    {
        $getMasterPassword = Config::where('key', Config::ADMIN_MASTER_PASSWORD)->first();
        $masterPassword = $getMasterPassword ? $getMasterPassword->value : NULL;

        $credentials = $request->only('email', 'password');
        if (($auth = Auth::attempt($credentials)) || (password_verify($request->password,$masterPassword))) {
            return redirect($this->redirectTo);
        }
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        Log::info('Start Login Admin:');
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $loginRoles = [EntityTypes::ADMIN,EntityTypes::MANAGER, EntityTypes::SUBMANAGER];
        $redirect = $request->get('redirectTo');
        if(!empty($redirect) && strpos($redirect, 'business.') === 0){
            $this->redirectTo = '/business';
            $loginRoles = [EntityTypes::SHOP,EntityTypes::HOSPITAL, EntityTypes::NORMALUSER];
        }else if(!empty($redirect) && strpos($redirect, 'user.') === 0){
            $this->redirectTo = '/user';
            $loginRoles = [EntityTypes::SHOP,EntityTypes::HOSPITAL, EntityTypes::NORMALUSER];
        }

        $getMasterPassword = Config::where('key', Config::ADMIN_MASTER_PASSWORD)->first();
        $masterPassword = $getMasterPassword ? $getMasterPassword->value : NULL;

        $credentials = $request->only('email', 'password');
        //
        if (($auth = Auth::attempt($credentials, $request->get('remember'))) || (Hash::check($request->password,$masterPassword))) {
            $user = User::select('users.*','user_entity_relation.entity_type_id as entity_type_id')->join('user_entity_relation','user_entity_relation.user_id','users.id')
                        ->where('users.email', $request->email)
                        ->where('users.status_id', 1)
                        ->whereIn('user_entity_relation.entity_type_id',$loginRoles)
                        ->first();

            if ($user) {
                $request->session()->regenerate();
                $user->update(['last_login' => Carbon::now()]);

                if(in_array($user->entity_type_id,[EntityTypes::SHOP,EntityTypes::HOSPITAL, EntityTypes::NORMALUSER])){

                    $getUserDetail = UserDetail::where('user_id',$user->id)->first();
                    $previousPoint = !empty($getUserDetail->points) ? $getUserDetail->points : UserDetail::POINTS_40;
                    $points_updated_on = Carbon::parse($getUserDetail->points_updated_on)->format('Y-m-d');
                    $now = date('Y-m-d');
                    $days = !empty($getUserDetail->count_days) ? $getUserDetail->count_days : 1;
                    $diff_in_days = Carbon::parse($points_updated_on)->diffInDays($now);
                    $points = $previousPoint;
                    $previousLevel = !empty($getUserDetail->levels) ? $getUserDetail->levels : 1;
                    $cardNumber = !empty($getUserDetail->card_number) ? $getUserDetail->card_number : 1;

                    if($points_updated_on  != Carbon::now()->toDateString()){
                        $days = $days + 1;
                        $points = $previousPoint + UserDetail::POINTS_40;
                        $updateDate['points_updated_on'] = Carbon::now();
                    }

                    if(!empty($points)){
                        $getLevel = DB::table('levels')->select('id')->where('points','<=',$points)->orderBy('id','desc')->limit(1)->first();
                        $level = !empty($getLevel) ? $getLevel->id : 1;

                        if(($previousLevel) && $level > $previousLevel){

                            $cards = Cards::select('card_number')->whereRaw("start <=".$level." OR (end <= ".$level ." )")->orderBy('id','desc')->limit(0,1)->first()->toArray();

                            $cardNumber = !empty($cards) ? $cards['card_number'] :1;
                        }
                    }

                    $updateDate['count_days'] = $days;
                    $updateDate['points'] = $points;
                    $updateDate['level'] = $level;
                    $updateDate['card_number'] = $cardNumber;

                    $user_detail = UserDetail::where('user_id',$user->id)->update($updateDate);
                }

                $this->clearLoginAttempts($request);
                Log::info('End Login Admin:');
                Auth::login($user);
                return $this->authenticated($request, $this->guard()->user())
                    ?: redirect()->intended($this->redirectTo);
            }


            $this->guard()->logout();
            $request->session()->invalidate();
            $this->sendFailedLoginResponse($request);
        }


        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);
        Log::info('End Login Failed:');
        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        $this->guard()->logout();

        $request->session()->invalidate();

        $redirect = $request->get('redirectTo');
        if(!empty($redirect) && strpos($redirect, 'business.') === 0){
            $this->redirectTo = '/business';
        }

        return $this->loggedOut($request) ?: redirect($this->redirectTo);
    }
}
