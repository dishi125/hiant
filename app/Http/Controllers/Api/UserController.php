<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\DeleteAccountReasonMail;
use App\Models\DeleteAccountReason;
use App\Models\PostLanguage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function viewProfile(Request $request)
    {
        $user = Auth::user();
        try {
            if($user){
                DB::beginTransaction();

                $data['name'] = $user->name;
                $data['avatar'] = $user->avatar;
                $data['email'] = $user->email;
                $data['phone_code'] = $user->phone_code;
                $data['mobile'] = $user->mobile;
                $data['gender'] = $user->gender;
                $data['recommended_code'] = $user->recommended_code;

                DB::commit();
                return $this->sendSuccessResponse("User profile details.", 200, $data);
            }else{
                return $this->sendSuccessResponse(Lang::get('messages.user.token_expired'), 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

    public function deleteMySelf(Request $request)
    {
        $inputs = $request->all();
        try{
            DB::beginTransaction();
            $user = Auth::user();
            $userID = $user->id;

            if(!empty($userID)){
                $password = $inputs['password'] ?? '';
                if(!Hash::check($password,Auth::user()->password)){
                    return $this->sendFailedResponse("Password is wrong.", 400);
                }

                if(!isset($inputs['reason'])){
                    return $this->sendFailedResponse("Please enter reason for remove account.", 400);
                }

                if(strlen(trim($inputs['reason'])) < 10){
                    return $this->sendFailedResponse("Please enter atleast 10 words in reason.", 400);
                }

                $toDate = Carbon::parse(date('Y-m-d H:i:s', strtotime($user->created_at)));
                $fromDate = Carbon::parse(Carbon::now()->format('Y-m-d H:i:s'));
                $months = $toDate->diffInMonths($fromDate);
                if ($months < 1){
                    $respData = [];
                    $toDate = Carbon::parse(date('Y-m-d H:i:s', strtotime('+1 month', strtotime($user->created_at))));
                    $diff_in_days = $toDate->diffInDays($fromDate);
                    $diff_in_hours = $toDate->diffInHours($fromDate);
                    $days = "days";
                    $day = "day";
                    $hours = "hours";
                    $hour = "hour";

                    if ($diff_in_hours >= 24){
                        $remain_cnt = ($diff_in_days>1)?($diff_in_days." $days"):($diff_in_days." $day");
                        $respData['count'] = $diff_in_days;
                        $respData['type'] = 'day';
                    }
                    else {
                        $remain_cnt = ($diff_in_hours>1)?($diff_in_hours." $hours"):($diff_in_hours." $hour");
                        $respData['count'] = $diff_in_hours;
                        $respData['type'] = 'hour';
                    }


                    $remain_delete = "Account deletion is possible $remain_cnt after account creation.";
                    return $this->sendFailedResponse($remain_delete, 500,$respData);
                }

                DeleteAccountReason::create([
                    'user_id' => $userID,
                    'reason' => $inputs['reason'],
                ]);

                //send mail to admin
                $mailData = (object)[
                    'username' => $user->name,
                    'phone' => $user->mobile,
                    'reason' => $inputs['reason'],
                    'signup_date' => $user->created_at,
                ];
                DeleteAccountReasonMail::dispatch($mailData);
            }

            DB::commit();
            return $this->sendSuccessResponse("Your account deletion request has been successfully completed.", 200, []);
        }catch(\Throwable $e){
            DB::rollBack();
            Log::info($e);
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

}
