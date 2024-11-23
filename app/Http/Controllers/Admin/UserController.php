<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EntityTypes;
use App\Models\Status;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserEntityRelation;
use App\Models\UserReferral;
use App\Models\UserReferralDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:user-list', ['only' => ['index']]);
    }

    public function index(Request $request)
    {
        $title = 'All User';
        DB::table('users')->where('is_admin_read', 1)->update(['is_admin_read' => 0]);

        $unreadReferralCount = DB::table('users_detail')->join('users', 'users.id', 'users_detail.user_id')->whereNull('users.deleted_at')->whereNotNull('users_detail.recommended_by')->where('users_detail.is_referral_read', 1)->count();
        return view('admin.users.index', compact('title', 'unreadReferralCount'));
    }

    public function getJsonAllData(Request $request)
    {
        $columns = array(
            0 => 'users_detail.name',
            1 => 'users.email',
            2 => 'users_detail.mobile',
            3 => 'users.created_at',
            4 => 'users.last_login',
        );

        $filter = $request->input('filter');
        $limit = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');
        $draw = $request->input('draw');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $adminTimezone = $this->getAdminUserTimezone();
        $toBeShopButton = '';
        $loginUser = Auth::user();
        try {
            $data = [];
            $userIDs = [];
            if ($filter == 'user') {
                $userIDs = DB::table('users')->leftJoin('user_entity_relation', 'user_entity_relation.user_id', 'users.id')
                    ->whereIN('user_entity_relation.entity_type_id', [EntityTypes::HOSPITAL, EntityTypes::SHOP])
                    ->whereNotNull('users.email')
                    ->whereNull('users.deleted_at')
                    ->pluck('users.id');
                $userIDs = ($userIDs) ? $userIDs->toArray() : [];
            }
            $userQuery = DB::table('users')->leftJoin('user_entity_relation', 'user_entity_relation.user_id', 'users.id')
                ->leftJoin('users_detail', 'users_detail.user_id', 'users.id')
                ->whereIN('user_entity_relation.entity_type_id', [EntityTypes::NORMALUSER, EntityTypes::HOSPITAL, EntityTypes::SHOP])
                ->whereNotNull('users.email')
                ->whereNull('users.deleted_at')
                ->whereIn('users.status_id', [Status::ACTIVE, Status::INACTIVE])
                ->whereNotIn('users.id', $userIDs)
                ->select(
                    'users.id',
                    'users_detail.name',
                    'users_detail.level',
                    'users_detail.mobile',
                    'users_detail.recommended_by',
                    'users_detail.recommended_code',
                    'users.inquiry_phone',
                    'users.email',
                    'users.is_admin_access',
                    'users.is_support_user',
                    'users.created_at as date',
                    'users.last_login as last_access',
                    DB::raw('(SELECT group_concat(entity_type_id) from user_entity_relation WHERE user_id = users.id) as entity_types')
                )
                ->selectSub(function ($q) {
                    $q->select('ref.name as referred_by_name')->from('users_detail as ref')->join('users as ru', 'ru.id', 'ref.user_id')->whereNull('ru.deleted_at')->whereIn('ru.status_id', [Status::ACTIVE, Status::INACTIVE])->whereRaw("`ref`.`user_id` = `users_detail`.`recommended_by`");
                }, 'referred_by_name')
                ->groupBy('users.id');

            if (!empty($search)) {
                $userQuery = $userQuery->where(function ($q) use ($search) {
                    $q->where('users_detail.name', 'LIKE', "%{$search}%")
                        ->orWhere('users.email', 'LIKE', "%{$search}%")
                        ->orWhere('users.created_at', 'LIKE', "%{$search}%")
                        ->orWhere('users_detail.mobile', 'LIKE', "%{$search}%");
                });
            }

            if ($filter == 'admin_user') {
                $userQuery = $userQuery->where('users.is_admin_access',1);
            }
            if ($filter == 'support_user') {
                $userQuery = $userQuery->where('users.is_support_user',1);
            }

            $userQuery = $userQuery->selectSub(function ($q) {
                $q->select(DB::raw('count(users_detail.id) as count'))->from('users_detail')->whereRaw("`users_detail`.`recommended_by` = `users`.`id`");
            }, 'referral_count');
            // Count Number
            $userQuery = $userQuery->selectSub(function ($q) {
                $q->select(DB::raw('count(users_detail.id) as count'))->from('users_detail')->where('users_detail.is_referral_read', 1)->whereRaw("`users_detail`.`recommended_by` = `users`.`id`");
            }, 'new_referral_count');

            if ($filter == 'referred-user') {
                // Max date for order
                $userQuery = $userQuery->selectSub(function ($q) {
                    $q->select(DB::raw('MAX(users_detail.created_at)'))->from('users_detail')->whereNull('users_detail.deleted_at')->whereRaw("`users_detail`.`recommended_by` = `users`.`id`");
                }, 'max_referral_date');
                $userQuery = $userQuery->havingRaw('referral_count > 0')->orderByRaw('max_referral_date DESC');
            }

            $totalData = count($userQuery->get());
            $totalFiltered = $totalData;
            $userQuery = $userQuery->offset($start)->limit($limit);
            if ($filter != 'referred-user') {
                $userQuery = $userQuery->orderBy($order, $dir);
            }
            $userData = $userQuery->get();
            $count = 0;
            foreach ($userData as $user) {
                $style = ($user->is_admin_access == 1) ? "color:deeppink" : '';
                $referral_code = "<p style='margin: 0'>Referral code: <span class='copy_code'>$user->recommended_code</span></p>";
                if($user->recommended_by!=null) {
                    $signup_code = UserDetail::where('user_id', $user->recommended_by)->pluck('recommended_code')->first();
                    $signup_via = "<p style='margin: 0'>Signup via: $signup_code</p>";
                }
                else {
                    $signup_via = '<div class="d-flex align-items-center">
                                    <input type="text" name="signup_code" id="signup_code" placeholder="Enter code">
                                    <input type="submit" class="btn btn-dark ml-1" value="Save" user-id="'.$user->id.'" id="btn_save_signup_code">
                            </div>';
                }
                if (!empty($user->referred_by_name)) {
                    $referredByButton = "<a role='button' href='javascript:void(0);' onClick='showReferralDetail({$user->recommended_by})' title='' data-original-title='View' class='btn btn-primary btn-sm mt-2' data-toggle='tooltip'>{$user->referred_by_name}</a>";
                } else {
                    $referredByButton = '';
                }
                $new_referral_count_div = "";
                if ($user->new_referral_count && $user->new_referral_count > 0) {
                    $new_referral_count_div = "<span class='list_unread_referral_count unread_referral_count'>{$user->new_referral_count}</span>";
                }

                $data[$count]['name'] = "<div class='d-flex align-items-center'>
                                        <p style='$style;margin: 0'>$user->name</p>
                                        <a role='button' onclick='editUsername(`" . route('admin.user.get-edit-username', [$user->id]) . "`)' title='' data-original-title='Edit Username' class='mx-1 btn btn-primary btn-sm' data-toggle='tooltip'>Edit</a>
                                        </div>" .$referral_code.$signup_via. $referredByButton;
                $data[$count]['email'] = $user->email;
                $data[$count]['phone'] = '<span class="copy_clipboard">' . $user->mobile . '</span>';
                $data[$count]['signup'] = $this->formatDateTimeCountryWise($user->date, $adminTimezone);
                $data[$count]['last_access'] = $this->formatDateTimeCountryWise($user->last_access, $adminTimezone);
                $data[$count]['referral'] = "<div class='position-absolute'>{$new_referral_count_div}</div>";

                $deleteButton = "<a role='button' href='javascript:void(0)' onclick='deleteUser(" . $user->id . ")' title='' data-original-title='Delete Account' class='mx-1 btn btn-primary btn-sm' data-toggle='tooltip'>Delete</a>";
                $editButton = "<a role='button' href='javascript:void(0)' onclick='editPassword(" . $user->id . ")' title='' data-original-title='Edit Account' class='mx-1 btn btn-primary btn-sm' data-toggle='tooltip'>Edit Password</a>";
                if ($loginUser->hasRole('Admin')) {
                    $editEmailButton = "<a role='button' href='javascript:void(0)' onclick='editEmail(`" . route('admin.user.get-edit-email', [$user->id]) . "`)' title='' data-original-title='Edit Account' class='mx-1 btn btn-primary btn-sm' data-toggle='tooltip'>Edit Email</a>";
                } else {
                    $editEmailButton = '';
                }
                $data[$count]['actions'] = "<div class='d-flex align-items-center'>$toBeShopButton $deleteButton $editButton $editEmailButton</div>";
                $count++;
            }

            if ($filter == 'referred-user') {
                // Reset count
                DB::table('users_detail')->where('is_referral_read', 1)->update(['is_referral_read' => 0]);
            }

            $jsonData = array(
                "draw" => intval($draw),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data
            );
            return response()->json($jsonData);
        } catch (Exception $ex) {
            Log::info('Exception in user list');
            Log::info($ex);
            $jsonData = array(
                "draw" => intval($draw),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            );
            return response()->json($jsonData);
        }
    }

    public function getEditUsername($id)
    {
        $user_detail = DB::table('users_detail')->where('user_id',$id)->whereNull('deleted_at')->first();
        $user = DB::table('users')->where('id',$id)->whereNull('deleted_at')->first();

        return view('admin.users.change-username-popup', compact('id', 'user_detail', 'user'));
    }

    public function editUsername(Request $request, $id)
    {
        $inputs = $request->all();
        $validator = Validator::make($inputs, [
            'username' => 'required'
        ], [
            'username.required' => 'Please enter user name.',
        ]);

        if ($validator->fails()) {
            return response()->json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ), 400);
        }

        DB::beginTransaction();
        try {
            UserDetail::where('user_id', $id)->update([
                'name' => $request->username,
                'gender' => $request->gender,
            ]);

            DB::commit();
            return response()->json(array(
                'success' => true,
                'message' => "User successfully updated."
            ), 200);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::info($ex);
            return response()->json(array(
                'success' => false,
                'message' => "Unable to update user"
            ), 400);
        }
    }

    public function addSignupCode(Request $request)
    {
        try {
            $inputs = $request->all();
            $recommended_user_id = UserDetail::where('recommended_code',$inputs['signup_code'])->pluck('user_id')->first();
            if ($recommended_user_id==null){
                $jsonData = [
                    'success' => false,
                    'message' => "Failed to add signup code!!",
                ];
                return response()->json($jsonData);
            }

            UserDetail::where('user_id',$inputs['user_id'])->update(['recommended_by' => $recommended_user_id]);
            $jsonData = [
                'success' => true,
                'message' => "Signup code added successfully.",
            ];
            return response()->json($jsonData);
        } catch (\Exception $ex) {
            $jsonData = [
                'success' => false,
                'message' => "Failed to add signup code!!",
            ];
            return response()->json($jsonData);
        }
    }

    public function showRefferalUser($id)
    {
        $adminTimezone = $this->getAdminUserTimezone();
        $referralUser = User::join('users_detail', 'users_detail.user_id', 'users.id')->select('users_detail.*', 'users_detail.name as display_name', 'users.email')->where('users.id', $id)->first();
        $users = UserDetail::join('users', 'users.id', 'users_detail.user_id')->select('users.*', 'users_detail.name', 'users_detail.mobile')->where('users_detail.recommended_by', $id)->withTrashed()->get();
        $cnt_referral = UserReferral::where('referred_by', $id)->count();

        return view('admin.users.show-referral-popup', compact('users', 'referralUser', 'adminTimezone', 'cnt_referral', 'id'));
    }

    public function getAccount($id)
    {
        return view('admin.users.delete-account', compact('id'));
    }

    public function getEditAccount($id)
    {
        return view('admin.users.edit-account', compact('id'));
    }

    public function editAccount(Request $request, $id)
    {
        try {
            Log::info('Start edit Password');
            $password = $request->password;

            if (!empty($password)) {
                $user = User::where('id', $id)->update([
                    'password' => Hash::make($password),
                ]);

                notify()->success("Password successfully updated.", "Success", "topRight");
            } else {
                notify()->error("Unable to Edit Password", "Error", "topRight");
            }

            return redirect()->route('admin.user.index');
        } catch (Exception $ex) {
            DB::rollBack();
            Log::info('Exception in edit Password');
            Log::info($ex);
            notify()->error("Unable to Edit Password", "Error", "topRight");
            return redirect()->route('admin.user.index');
        }
    }

    public function getEditEmail($id)
    {
        $userdata = DB::table('users')->whereId($id)->first();
        return view('admin.users.change-email-popup', compact('id', 'userdata'));
    }

    public function editEmailAddress(Request $request, $id)
    {
        $inputs = $request->all();
        $validator = Validator::make($inputs, [
            'email' => 'required|max:255|unique:users,email,' . $id
        ], [
            'email.unique' => 'This Email is already been taken.',
        ]);

        if ($validator->fails()) {
            return response()->json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ), 400);
        }

        try {
            $email = $request->email;
            if (!empty($email)) {
                $user = User::where('id', $id)->update([
                    'email' => $email,
                ]);
            }
            return response()->json(array(
                'success' => true,
                'message' => "Email successfully updated."
            ), 200);
        } catch (Exception $ex) {
            Log::info($ex);
            return response()->json(array(
                'success' => false,
                'message' => "Unable to update Email"
            ), 400);
        }
    }

    public function createUser()
    {
        $title = "Add User";
        return view('admin.users.form', compact('title'));
    }

    public function storeUser(Request $request)
    {
        $inputs = $request->all();
//        try {
            Log::info('Start code for the add user');
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'username' => 'required',
                'email' => 'required|max:255|unique:users,email,NULL,id,deleted_at,NULL|email',
                'phone_number' => 'required|numeric',
                'password' => 'required|min:6|confirmed',
                'password_confirmation' => 'required|min:6',
                'gender' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $user = User::create([
                "email" => $inputs['email'],
                'username' => $inputs['username'],
                "password" => Hash::make($inputs['password']),
                'status_id' => Status::ACTIVE,
            ]);
            UserEntityRelation::create(['user_id' => $user->id, "entity_type_id" => EntityTypes::NORMALUSER, 'entity_id' => $user->id]);
            $random_code = mt_rand(1000000, 9999999);
            $member = UserDetail::create([
                'user_id' => $user->id,
                'name' => trim($inputs['username']),
                'email' => $inputs['email'],
                'phone_code' => $inputs['phone_code'] ?? null,
                'mobile' => $inputs['phone_number'],
                'gender' => $inputs['gender'],
                'recommended_code' => $random_code,
                'points_updated_on' => Carbon::now(),
                'points' => UserDetail::POINTS_40,
            ]);

            Log::info('End code for the add user');
            DB::commit();
            notify()->success("User added successfully", "Success", "topRight");

            $redirect = 'admin.user.index';
            return redirect()->route($redirect);
        /*} catch (\Exception $e) {
            Log::info('Exception in the add user.');
            Log::info($e);
            notify()->error("Failed to add user", "Error", "topRight");
            return redirect()->route('admin.user.index');
        }*/
    }

}
