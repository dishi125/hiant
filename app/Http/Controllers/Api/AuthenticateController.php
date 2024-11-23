<?php

namespace App\Http\Controllers\Api;

use App\Mail\ForgotPassword;
use DB;
use Illuminate\Support\Facades\Storage;
use Log;
use Auth;
use Hash;
use Lang;
use JWTAuth;
use Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Cards;
use App\Models\Config;
use App\Models\Status;
use App\Util\Firebase;
use App\Models\Country;
use App\Jobs\SignupMail;
use App\Models\UserDetail;
use App\Models\EntityTypes;
use App\Models\UserDevices;
use Illuminate\Support\Str;
use App\Models\UserReferral;
use Illuminate\Http\Request;
use App\Validators\UserValidator;
use App\Models\UserEntityRelation;
use App\Models\UserReferralDetail;
use App\Validators\LoginValidator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthenticateController extends Controller
{
    private $userValidator;
    private $loginValidator;
    protected $firebase;

    public function __construct()
    {
        $this->userValidator = new UserValidator();
        $this->loginValidator = new LoginValidator();
        $this->firebase = new Firebase();
    }

    public function registerValidate(Request $request)
    {
        $inputs = $request->all();
        try {
            $language_id = $inputs['language_id'] ?? 4;
            $validation = $this->userValidator->validateRegister($inputs, $language_id);
            if ($validation->fails()) {
                return $this->sendCustomErrorMessage($validation->errors()->toArray(), 422);
            }
            return $this->sendSuccessResponse(Lang::get('messages.authenticate.validate_email'), 200);
        } catch (\Exception $e) {
            Log::info($e);
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

    /**
     * Register User
     *
     * Register new user as App User
     *
     * @param Request $request
     * @return void
     */
    public function register(Request $request)
    {
        try {
            $inputs = $request->all();
            $language_id = $request->has('language_id') ? $inputs['language_id'] : 4;
            $validation = $this->userValidator->validateStore($inputs, $language_id);
            if ($validation->fails()) {
                return $this->sendCustomErrorMessage($validation->errors()->toArray(), 422);
            }
            DB::beginTransaction();
            Log::info('Start code for the user register');
            $recommended_code = $inputs['recommended_code'] ?? '';
            $recommended_user = UserDetail::where('recommended_code', $recommended_code)->first();
//            $exp_amount = 0;
//            $exp_config = Config::where('key', Config::GIVE_REFERRAL_EXP)->first();
//            $exp_amount = $exp_config ? (int) filter_var($exp_config->value, FILTER_SANITIZE_NUMBER_INT) : 0;

//            $giveLoveCount = UserCards::REGISTER_LOVE_COUNT;
//            $registerCoin = UserCoinHistory::REGISTER_COIN;
//            $referral_register = UserCoinHistory::REFERRAL_REGISTER_COIN;
//            $referral_register_bonus = UserCoinHistory::REFERRAL_REGISTER_BONUS_COIN;

            $recommended_by = NULL;
            if ($recommended_user) {
                $user_phone_count = UserDetail::where('recommended_by', $recommended_user->user_id)->where('mobile', $inputs['phone'])->where('phone_code', $inputs['phone_code'])->count();
                $user = User::find($recommended_user->user_id);
                if ($user_phone_count == 0) {
//                    $giveLoveCount += UserCards::REFERRAL_LOVE_COUNT;
                    $recommended_by = $recommended_user->user_id;
//                    $isShop = $user->entityType->contains('entity_type_id', EntityTypes::SHOP);
//                    $isHospital = $user->entityType->contains('entity_type_id', EntityTypes::HOSPITAL);
//                    $add_credit = 0;

                    // Give EXP
//                    $userIds = [$recommended_user->user_id];
//                    $devices = UserDevices::whereIn('user_id', $userIds)->pluck('device_token')->toArray();

//                    $referralLove = UserCards::REFERRAL_LOVE_COUNT;
//                    $appliedCard = DB::table('user_cards')->where('user_id', $recommended_user->user_id)->where('is_applied', 1)->first();
                    /*if ($appliedCard) {
                        UserCards::whereId($appliedCard->id)->update(['love_count' => DB::raw("love_count + $referralLove")]);

                        $notify_type = Notice::LOVE_REFERRAL;
                        $love_notice_key = "language_$language_id.give_referral_exp";
                        $love_notice_msg = __("messages.$love_notice_key",['username' => trim($inputs['name'])]);

                        $referralCount = UserReferral::where('referred_by',$recommended_by)->where('has_coffee_access',0)->count();
                        $referralCount = $referralCount + 1;
                        $sub_title_display = "+10 Love, +1 Starbuck stamp($referralCount/3)";
                        $notice = Notice::create([
                            'notify_type' => $notify_type,
                            'user_id' => $recommended_user->user_id,
                            'to_user_id' => $recommended_user->user_id,
                            'title' => $love_notice_msg,
                            'sub_title' => trim($inputs['name']),
                            'entity_id' => $referralCount,
                        ]);

                        $format = $sub_title_display;
                        $notificationData = [
                            'id' => $recommended_user->user_id,
                            'user_id' => $recommended_user->user_id,
                            'title' => $love_notice_msg,
                        ];
                        if (count($devices) > 0) {
                            $result = $this->sentPushNotification($devices, $love_notice_msg, $format, $notificationData, $notify_type);
                        }

                        $checkLoveCount = $appliedCard->love_count + $referralLove;
                        $cardLevel = DB::table('card_levels')->whereRaw("(start <= " . $checkLoveCount . " AND end >= " . $checkLoveCount . ")")->first();

                        if ($cardLevel->id > $appliedCard->active_level) {
                            UserCards::whereId($appliedCard->id)->update(['active_level' => $cardLevel->id]);
                        }
                    }*/

                    $userDetails = DB::table('users_detail')->where('user_id', $recommended_user->user_id)->first();
                    $getLevel = DB::table('levels')->select('id')->where('points', '<=', $userDetails->points)->orderBy('id', 'desc')->first();
                    $updateLevel = !empty($getLevel) ? $getLevel->id : 1;

                    if (($userDetails) && $updateLevel > $userDetails->level) {
                        $cards = Cards::select('card_number')->whereRaw("start <=" . $updateLevel . " OR (end <= " . $updateLevel . " )")->orderBy('id', 'desc')->limit(0, 1)->first()->toArray();
                        $cardNumber = !empty($cards) ? $cards['card_number'] : 1;

                        UserDetail::where('user_id', $recommended_user->user_id)->update(['level' => $updateLevel, 'card_number' => $cardNumber]);
//                        $getUserOwnCardCount = UserCards::where(['user_id' => $recommended_user->user_id])->count();
                        /*if ($cardNumber > $getUserOwnCardCount) {
                            $getCardsByLevelQ = DefaultCardsRives::select('default_cards_rives.*')->leftjoin('default_cards as dc', 'dc.id', 'default_cards_rives.default_card_id');

                            $getCardsByLevelQ = $getCardsByLevelQ->whereRaw("(dc.start <= " . $updateLevel . " AND dc.end >= " . $updateLevel . " )");

                            $getCardsByLevelQ = $getCardsByLevelQ->whereNotIn('default_cards_rives.id', function ($q) use ($recommended_user) {
                                $q->select('default_cards_id')->from('user_cards')->where('user_id', $recommended_user->user_id);
                            });

                            $getCardsByLevelQ = $getCardsByLevelQ->inRandomOrder()->limit(1)->first();

                            if (!empty($getCardsByLevelQ)) {
                                $cardData = [
                                    'user_id' => $recommended_user->user_id,
                                    'default_cards_id' => $getCardsByLevelQ->default_card_id,
                                    'default_cards_riv_id' => $getCardsByLevelQ->id
                                ];
                                $userCard = UserCards::create($cardData);
                                createUserCardDetail($getCardsByLevelQ, $userCard);
                            }
                        }*/
                    }

//                    $devices = UserDevices::whereIn('user_id', [$recommended_user->user_id])->pluck('device_token')->toArray();
//                    $language_id = $userDetails->language_id ?? 4;
                    // Reward Coin
                    /*if ($isShop) {
                        $config = Config::where('key', Config::SHOP_RECOMMEND_MONEY)->first();
                    }
                    if ($isHospital) {
                        $config = Config::where('key', Config::HOSPITAL_RECOMMEND_MONEY)->first();
                    }
                    $new_credit = !empty($config) ? (int) filter_var($config->value, FILTER_SANITIZE_NUMBER_INT) : 0;
                    $userCredits = UserCredit::where('user_id', $recommended_user->user_id)->first();
                    $old_credit = $userCredits->credits ?? 0;
                    $total_credit = $old_credit + $new_credit;
                    $credit = UserCredit::where('user_id', $recommended_user->user_id)->update(['credits' => $total_credit]);
                    $creditHistory = UserCreditHistory::create([
                        'user_id' => $recommended_user->user_id,
                        'amount' => $new_credit,
                        'total_amount' => $total_credit,
                        'transaction' => 'credit',
                        'type' => UserCreditHistory::RECOMMENDED
                    ]);*/
                } /*else {
                    $notice = Notice::create([
                        'notify_type' => Notice::REWARD_RECOMMENDED_ONCE,
                        'user_id' => $user->id,
                        'to_user_id' => $user->id,
                    ]);
                }*/
            }

//            $non_login_user_id = $inputs['user_id'] ?? '';
//            $manager = Manager::where('recommended_code', $recommended_code)->first();
//            $managerID = !empty($manager) ? $manager->id : null;
            $country = Country::where('phonecode', $inputs['phone_code'])->first();

            $user = User::create([
                "email" => $inputs['email'],
                'username' => $inputs['email'],
                "password" => Hash::make($inputs['password']),
                'status_id' => Status::ACTIVE,
            ]);

            UserEntityRelation::create(['user_id' => $user->id, "entity_type_id" => EntityTypes::NORMALUSER, 'entity_id' => $user->id]);
            $random_code = mt_rand(1000000, 9999999);

            $userGetLevel = DB::table('levels')->select('id')->where('points', '<=', (UserDetail::POINTS_40))->orderBy('id', 'desc')->first();
            $userUpdateLevel = !empty($userGetLevel) ? $userGetLevel->id : 1;

            if ($request->hasFile('avatar')) {
                $profileFolder = config('constant.profile');
                if (!Storage::exists($profileFolder)) {
                    Storage::makeDirectory($profileFolder);
                }
                $avatar = Storage::disk('s3')->putFile($profileFolder, $request->file('avatar'),'public');
                $fileName = basename($avatar);
                $avatar = $profileFolder . '/' . $fileName;
            }
            $member = UserDetail::create([
                'user_id' => $user->id,
                'country_id' => $country->id,
                'name' => trim($inputs['name']),
                'email' => $inputs['email'],
                'phone_code' => $inputs['phone_code'],
                'mobile' => $inputs['phone'],
                'gender' => $inputs['gender'],
                'device_type_id' => $inputs['device_type_id'],
                'device_id' => $inputs['device_id'],
                'device_token' => $inputs['device_token'],
                'recommended_code' => $random_code,
                'recommended_by' => $recommended_by,
                'points_updated_on' => Carbon::now(),
                'points' => (UserDetail::POINTS_40), //  + $exp_amount
                'level' => $userUpdateLevel,
//                'manager_id' => $managerID,
                'mbti' => isset($inputs['mbti']) ? trim($inputs['mbti']) : null,
                'avatar' => isset($avatar) ? $avatar : null,
            ]);

            UserDevices::create(['user_id' => $user->id, 'device_token' => $inputs['device_token']]);

            // Referral Logs
            if($recommended_by){
                UserReferral::create([
                    'referred_by' => $recommended_by,
                    'referral_user' => $user->id
                ]);

                $referralCount = UserReferral::where('referred_by',$recommended_by)->where('has_coffee_access',0)->count();

                if(!empty($referralCount) && $referralCount >= 3){
                    UserReferralDetail::create([
                        'user_id' => $recommended_by
                    ]);
                    UserReferral::where('referred_by',$recommended_by)->where('has_coffee_access',0)->take(3)->update(['has_coffee_access' => 1]);
                }

                //send mail to admin
                $mailData = (object)[
                    'email' => $inputs['email'],
                    'username' => $inputs['name'],
                    'gender' => $inputs['gender'],
                    'phone' => $inputs['phone'],
                    'subject' => "[Juju] New referral sign up"
                ];
                SignupMail::dispatch($mailData);
            }
            // Referral Logs

            // Assign Default Card Start
            /*$getDefaultCard = DefaultCards::select('dcr.default_card_id', 'dcr.id as riv_id', 'dcr.background_rive', 'dcr.character_rive')->leftJoin('default_cards_rives as dcr', 'default_card_id', 'default_cards.id')->where('default_cards.name', DefaultCards::DEFAULT_CARD)->first();

            $default_cards_id = (!empty($getDefaultCard)) ? $getDefaultCard->default_card_id : 0;
            $default_cards_riv_id = (!empty($getDefaultCard)) ? $getDefaultCard->riv_id : 0;
            $background_rive = (!empty($getDefaultCard)) ? $getDefaultCard->background_rive : NULL;
            $character_rive = (!empty($getDefaultCard)) ? $getDefaultCard->character_rive : NULL;

            $totalLove = NonLoginLoveDetails::where('device_id', $inputs['device_id'])->count();
            $currentLevel = '';

            $card_level_status = UserCards::NORMAL_STATUS;
            $giveLoveCount += $totalLove;

            $currentLevel = DB::table('card_levels')
                ->whereRaw("(card_levels.start <= " . $giveLoveCount . " AND card_levels.end >= " . $giveLoveCount . ")")
                ->first();

            if (!empty($totalLove)) {
                $checkDate = Carbon::now()->subHour();
                $last_feed = NonLoginLoveDetails::where('device_id', $inputs['device_id'])->where('card_log', UserCardLog::FEED)->whereDate('created_at', Carbon::now())->orderBy('created_at', 'DESC')->first();

                if (!empty($last_feed) && Carbon::parse($checkDate)->lt($last_feed->created_at)) {
                    $card_level_status = UserCards::HAPPY_STATUS;
                }
            }

            $cardWhere = [
                'user_id' => $user->id,
                'default_cards_id' => $default_cards_id,
                'default_cards_riv_id' => $default_cards_riv_id,
            ];

            $cardDetail = [
                'is_applied' => 1,
                'card_level' => 1,
                'active_level' => ($currentLevel) ? $currentLevel->id : 1,
                'love_count' => $giveLoveCount,
                'card_level_status' => $card_level_status,
            ];

            $assignDefualtCard = UserCards::updateOrCreate($cardWhere, $cardDetail);

            if ($default_cards_riv_id) {
                $cardRiveData = DefaultCardsRives::find($default_cards_riv_id);
                createUserCardDetail($cardRiveData, $assignDefualtCard);
            }

            if (!empty($totalLove)) {
                $loveDetails = NonLoginLoveDetails::where('device_id', $inputs['device_id'])->get();
                foreach ($loveDetails as $detail) {
                    $love_count = UserCards::where('user_id',$user->id)->where('is_applied',1)->pluck('love_count')->first();
                    UserCardLog::create([
                        'user_id' => $user->id,
                        'card_id' => $assignDefualtCard->id,
                        'card_log' => UserCardLog::FEED,
                        'created_at' => $detail->created_at,
                        'love_count' => (empty($love_count)) ? 0 : $love_count
                    ]);

                    UserFeedLog::updateOrCreate([
                        'user_id' => $user->id,
                    ],[
                        'card_id' => $assignDefualtCard->id,
                        'feed_time' => $detail->created_at,
                    ]);
                }
                NonLoginLoveDetails::where('device_id', $inputs['device_id'])->delete();
            }*/
//            NonLoginUserDetail::where('device_id', $inputs['device_id'])->delete();
            // Assign Default Card End

            // Instagram Start
            /*$access_token = $inputs['access_token'] ?? '';
            $social_type = $inputs['social_type'] ?? '';
            $social_id = $inputs['social_id'] ?? '';
            $social_name = $inputs['social_name'] ?? '';

            if ((!empty($managerID)) || (!empty($access_token) && !empty($social_type) && $social_type == LinkedSocialProfile::Instagram && !empty($social_id) && !empty($social_name))) {
                User::whereId($user->id)->update([
                    'inquiry_phone' => (isset($inputs['inquiry_phone']) && $inputs['inquiry_phone'] == true),
                    'connect_instagram' => (isset($inputs['connect_instagram']) && $inputs['connect_instagram'] == true)
                ]);
                $category = Category::where('category_type_id', CategoryTypes::SHOP)->first();
                $business_category_id = $inputs['business_category_id'] ?? $category->id;

                $shop = Shop::create([
                    'email' => $inputs['email'] ?? NULL,
                    'mobile' => $inputs['phone'],
                    'shop_name' => NULL,
                    'best_portfolio' => NULL,
                    'business_licence' => NULL,
                    'identification_card' => NULL,
                    'business_license_number' => '',
                    'status_id' => Status::PENDING,
                    'category_id' => $business_category_id,
                    'user_id' => $user->id,
                    'manager_id' => '',
                    'uuid' => (string) Str::uuid(),
                    'credit_deduct_date' => Carbon::now()->toDateString()
                ]);
                $entity_id = $shop->id;

                syncGlobalPriceSettings($entity_id,$inputs['language_id'] ?? 4);

                UserDetail::where('user_id',$user->id)->update([
                    'package_plan_id' => PackagePlan::BRONZE,
                    'plan_expire_date' => Carbon::now()->addDays(30),
                    'last_plan_update' => Carbon::now()
                ]);

                $config = Config::where('key', Config::BECAME_SHOP)->first();

                UserEntityRelation::create([
                    'user_id' => $user->id,
                    'entity_type_id' => EntityTypes::SHOP,
                    'entity_id' => $entity_id,
                ]);

                $defaultCredit = $config ? (int) filter_var($config->value, FILTER_SANITIZE_NUMBER_INT) : 150000;
                $credit = UserCredit::updateOrCreate([
                    'user_id' => $user->id,
                    'credits' => DB::raw("credits + $defaultCredit")
                ]);

                if (!empty($access_token) && !empty($social_type) && $social_type == LinkedSocialProfile::Instagram && !empty($social_id) && !empty($social_name)) {
                    $access_token = $inputs['access_token'] ?? null;
                    LinkedSocialProfile::updateOrCreate([
                        'social_type' => $social_type,
                        'shop_id' => $entity_id,
                        'user_id' => $user->id
                    ], [
                        'social_id' => $social_id,
                        'access_token' => $access_token,
                        'social_name' => $social_name,
                        'token_refresh_date' => Carbon::now(),
                    ]);

                    InstagramLog::create([
                        "social_id" =>$social_id,
                        "user_id" =>$user->id,
                        "shop_id" =>$entity_id,
                        "social_name" =>$social_name,
                        "status" =>InstagramLog::CONNECTED,
                    ]);

                    LinkedProfileHistory::updateOrCreate([
                        'shop_id' => $entity_id,
                        'social_id' => $social_id
                    ], [
                        'social_name' => $social_name,
                        'access_token' => $access_token
                    ]);

                    // Mail
                    $mailData = (object)[
                        'social_name' => $social_name,
                        'email' => $inputs['email'],
                        'username' => $inputs['name'],
                        'gender' => $inputs['gender'],
                        'phone' => $inputs['phone'],
                        'shop_id' => $entity_id,
                    ];

                    $config = Config::where('key', Config::INSTAGRAM_REGISTER_PUSH_EMAIL)->first();
                    if(!empty($config->value)){
                        $emailIds = explode(',',$config->value);
                        if(!empty($emailIds)){
                            foreach ($emailIds as $email) {
                                Mail::to($email)->send(new RegisterAccountMail($mailData));
                            }
                        }
                    }
                }
            }*/
            // Instagram End

            $token = JWTAuth::fromUser($user);
            $user = User::with(['entityType'])->where('id', $user->id)->where('status_id', Status::ACTIVE)->first();
            $user_detail = UserDetail::where('user_id', $user->id)->first();
//            $user['hospital_count'] = UserEntityRelation::where('entity_type_id', EntityTypes::HOSPITAL)->where('user_id', $user->id)->count();
            $user['shop_count'] = UserEntityRelation::where('entity_type_id', EntityTypes::SHOP)->where('user_id', $user->id)->count();
            $user['hide_popup'] = !empty($user_detail) ? $user_detail->hide_popup : 0;

//            if ((empty($access_token) && empty($social_type) && empty($social_name))) {
                // Mail for simple user
                $mailData = (object)[
                    'email' => $inputs['email'],
                    'username' => $inputs['name'],
                    'gender' => $inputs['gender'],
                    'phone' => $inputs['phone'],
                    'subject' => "[Juju] New sign up"
                ];
                SignupMail::dispatch($mailData);
//            }

            /*$hospital_name = "";
            if ($user['hospital_count'] > 0) {
                $entityData = UserEntityRelation::where('entity_type_id', EntityTypes::HOSPITAL)->where('user_id', $user->id)->first();
                $hospital = Hospital::find($entityData->entity_id);
                $hospital_name = $hospital ? $hospital->main_name : "";
            }
            $user['hospital_name'] = $hospital_name;*/

//            $devices = [$inputs['device_token']];
//            $language_id = $inputs['language_id'] ?? 4;

            // Give coin to USER
            /*UserCoinHistory::create([
                'user_id' => $user->id,
                'amount' => $registerCoin,
                'type' => UserCoinHistory::REGISTER,
                'transaction' => UserCoinHistory::CREDIT,
                'entity_id' => $user->id,
            ]);*/
            // Give coin to USER

            /*if (!empty($recommended_by)) {
                // Give coin to Referral Owner USER
                UserCoinHistory::create([
                    'user_id' => $recommended_by,
                    'amount' => $referral_register,
                    'type' => UserCoinHistory::REFERRAL_REGISTER,
                    'transaction' => UserCoinHistory::CREDIT,
                    'entity_id' => $user->id,
                ]);
                // Give coin to Referral Owner USER

                // Give coin to USER
                UserCoinHistory::create([
                    'user_id' => $user->id,
                    'amount' => $referral_register_bonus,
                    'type' => UserCoinHistory::REFERRAL_REGISTER_BONUS,
                    'transaction' => UserCoinHistory::CREDIT,
                    'entity_id' => $recommended_by,
                ]);
                // Give coin to USER
            }*/

           /* if(!empty($non_login_user_id)){
                UserHiddenCategory::where('user_id',$non_login_user_id)
                    ->where('user_type',UserHiddenCategory::NONLOGIN)
                    ->update([
                        'user_id' => $user->id,
                        'user_type' => UserHiddenCategory::LOGIN
                    ]);
            }*/
            $user['token'] = $token;
            DB::commit();
            return $this->sendSuccessResponse(Lang::get('messages.authenticate.success'), 200, $user);
        } catch (\Exception $e) {
            Log::info('Exception in the user register');
            Log::info($e);
            DB::rollBack();
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

    /**
     * User Login
     *
     * @param Request $request
     * @return void
     */
    public function login(Request $request)
    {
        try {
            $inputs = $request->all();
            $validation = $this->loginValidator->validateStore($inputs);
            $language_id = $inputs['language_id'] ?? 4;

            if ($validation->fails()) {
                return $this->sendCustomErrorMessage($validation->errors()->toArray(), 422);
            }

            $credentials = $request->only('email', 'password');

            $expireMasterPassword = Config::expirePassword();  // expires password after 24 hours from updated date.
            $getMasterPassword = Config::where('key', Config::ADMIN_MASTER_PASSWORD)->first();
            $masterPassword = $getMasterPassword ? $getMasterPassword->value : NULL;

            if (!$token = JWTAuth::attempt($credentials) || (Hash::check($request->password, $masterPassword) !== 1)) {
                return $this->sendFailedResponse(Lang::get(__("messages.language_$language_id.cred_invalid")), 422);
            }

            $user = null;
            if ($token = JWTAuth::attempt($credentials) || (Hash::check($request->password, $masterPassword))) {
                $user = User::with(['entityType'])->where('email', $request->email)->where('status_id', Status::ACTIVE)->first();

                if ($user) {
                    $token = JWTAuth::fromUser($user);
                    Auth::login($user);
                } else {
                    return $this->sendFailedResponse(Lang::get(__("messages.language_$language_id.cred_invalid")), 422);
                }
            }

            if ($request->has('device_token') && !empty($user)) {
                UserDevices::firstOrCreate(['user_id' => Auth::user()->id, 'device_token' => $inputs['device_token']]);
                UserDetail::where('user_id', Auth::user()->id)->update(['device_token' => $inputs['device_token']]);
            }

            if ($user) {
                $user->update(['last_login' => Carbon::now()]);
                $user_detail = UserDetail::where('user_id', Auth::user()->id)->first();

//                $appliedCardCount = UserCards::where(['user_id' => Auth::user()->id, 'is_applied' => 1])->first();
                // Assign Default if not Assigned Card Start
//                $getDefaultCard = DefaultCards::select('dcr.default_card_id', 'dcr.id as riv_id', 'dcr.background_rive', 'dcr.character_rive')->leftJoin('default_cards_rives as dcr', 'default_card_id', 'default_cards.id')->where('default_cards.name', DefaultCards::DEFAULT_CARD)->first();

//                $default_cards_id = (!empty($getDefaultCard)) ? $getDefaultCard->default_card_id : 0;
//                $default_cards_riv_id = (!empty($getDefaultCard)) ? $getDefaultCard->riv_id : 0;
//                $background_rive = (!empty($getDefaultCard)) ? $getDefaultCard->background_rive : NULL;
//                $character_rive = (!empty($getDefaultCard)) ? $getDefaultCard->character_rive : NULL;

               /* $cardWhere = [
                    'user_id' => Auth::user()->id,
                    'default_cards_id' => $default_cards_id,
                    'default_cards_riv_id' => $default_cards_riv_id,
                ];

                $cardDetail = [
                    'is_applied' => (!empty($appliedCardCount) && $default_cards_riv_id != $appliedCardCount->default_cards_riv_id) ? 0 : 1
                ];*/

//                $assignDefualtCard = UserCards::updateOrCreate($cardWhere, $cardDetail);
                // Assign Default Card End
                /*if ($default_cards_riv_id) {
                    $cardRiveData = DefaultCardsRives::find($default_cards_riv_id);
                    createUserCardDetail($cardRiveData, $assignDefualtCard);
                }*/

                $user['hide_popup'] = !empty($user_detail) ? $user_detail->hide_popup : 0;

//                $user['hospital_count'] = UserEntityRelation::where('entity_type_id', EntityTypes::HOSPITAL)->where('user_id', $user->id)->count();
                $user['shop_count'] = UserEntityRelation::where('entity_type_id', EntityTypes::SHOP)->where('user_id', $user->id)->count();
                /*$hospital_name = "";
                if ($user['hospital_count'] > 0) {
                    $entityData = UserEntityRelation::where('entity_type_id', EntityTypes::HOSPITAL)->where('user_id', $user->id)->first();
                    $hospital = Hospital::find($entityData->entity_id);
                    $hospital_name = $hospital ? $hospital->main_name : "";
                }
                $user['hospital_name'] = $hospital_name;*/
                $user['token'] = $token;
                return $this->sendSuccessResponse(Lang::get('messages.authenticate.success'), 200, $user);
            } else {
                if ($token = JWTAuth::getToken()) {
                    JWTAuth::invalidate($token);
                }
                return $this->sendFailedResponse(Lang::get(__("messages.language_$language_id.cred_invalid")), 422);
            }
        } catch (JWTException $ex) {
            Log::info('Exception in the user Login');
            Log::info($ex);
            return $this->sendFailedResponse(Lang::get(__("messages.language_$language_id.cred_invalid")), 500);
        }
    }

    /**
     * User Logout
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logOut(Request $request)
    {
        $inputs = $request->all();

        if ($request->has('device_token') && !empty($inputs['device_token'])) {
            UserDevices::where('user_id', Auth::user()->id)->where('device_token', $inputs['device_token'])->delete();
        }
        // $user = User::where('id', Auth::user()->id)->update(['last_login' => Carbon::now()]);
        UserDetail::where('user_id', Auth::user()->id)->update(['device_token' => '']);
        if ($token = JWTAuth::getToken()) {
            JWTAuth::invalidate($token);
        }
        return $this->sendSuccessResponse(Lang::get('messages.authenticate.logout'), 200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $data['token'] = JWTAuth::refresh();
        $data['user'] = auth()->user();
        return $this->sendSuccessResponse(Lang::get('messages.authenticate.success'), 200, $data);
    }

    public function forgotEmail(Request $request)
    {
        try {
            Log::info('start code for user forgot mail');
            $inputs = $request->all();

            $validation = $this->loginValidator->validateForgotEmail($inputs);

            if ($validation->fails()) {
                return $this->sendCustomErrorMessage($validation->errors()->toArray(), 422);
            }

            $user_detail = UserDetail::where('phone_code', $inputs['phone_code'])->where('mobile', $inputs['phone'])->pluck('user_id')->toArray();
            if (!empty($user_detail)) {
                $user = User::whereIn('id', $user_detail)->get(['email']);
                $user->makeHidden(['display_created_at','all_entity_type_id','entity_type_id','name','gender','phone_code','mobile','recommended_code','verify_status','sns_type','sns_link','mbti','avatar']);
                Log::info('End code for user forgot mail');
                return $this->sendSuccessResponse(Lang::get('messages.forgot-email.success'), 200, $user);
            } else {
                return $this->sendFailedResponse(Lang::get('messages.forgot-email.mobile-not-exist'), 400);
            }
        } catch (\Exception $ex) {
            Log::info('Exception in forgot mail');
            Log::info($ex);
            DB::rollBack();
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 422);
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            Log::info('start code for user forgot password send mail');
            $inputs = $request->all();

            $validation = $this->loginValidator->validateForgotPassword($inputs);

            if ($validation->fails()) {
                return $this->sendCustomErrorMessage($validation->errors()->toArray(), 422);
            }
            $user = User::where('email', $inputs['email'])->first();
            if (!empty($user)) {
                $otp = mt_rand(100000, 999999);
                $user->otp = $otp;
                Mail::to($user->email)->send(new ForgotPassword($user));
                Log::info('End code for user forgot password send mail');
                return $this->sendSuccessResponse(Lang::get('messages.forgot-password.mail-success'), 200, $otp);
            } else {
                return $this->sendFailedResponse(Lang::get('messages.forgot-password.mobile-not-exist'), 422);
            }
        } catch (\Exception $e) {
            Log::info('Exception in forgotpassword mail');
            Log::info($e);
            DB::rollBack();
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

    public function changePassword(Request $request)
    {
        Log::info('start code for change password');
        $inputs = $request->all();
        $validation = $this->loginValidator->validateUpdatePassword($inputs);

        if ($validation->fails()) {
            return $this->sendCustomErrorMessage($validation->errors()->toArray(), 422);
        }
        $email = $inputs['email'];
        $password = $inputs['password'];
        $user_detail = User::where('email', $email)->first();
        try {
            if (!empty($user_detail)) {
                $user = User::where('id', $user_detail->id)->update([
                    'password' => Hash::make($password),
                ]);
                Log::info('End code for change password');
                return $this->sendSuccessResponse(Lang::get('messages.change-password.success'), 200);
            } else {
                return $this->sendFailedResponse(Lang::get('messages.forgot-password.email-not-exist'), 400);
            }
        } catch (\Exception $e) {
            Log::info('Exception in forgotpassword mail');
            Log::info($e);
            DB::rollBack();
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

}
