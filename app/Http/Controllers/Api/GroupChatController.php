<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GroupBlockedUser;
use App\Models\GroupChatRoom;
use App\Models\GroupChatRoomJoinedUser;
use App\Models\GroupChatRoomKeyword;
use App\Models\Keyword;
use App\Models\ReportedUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GroupChatController extends Controller
{
    public function createGroup(Request $request)
    {
        $user = Auth::user();
        $inputs = $request->all();

        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'group_image' => 'image|mimes:jpeg,jpg,gif,svg,png',
            ], [], [
                'title' => 'Title',
            ]);
            if ($validator->fails()) {
                return $this->sendCustomErrorMessage($validator->errors()->toArray(), 422);
            }

            if($user){
                DB::beginTransaction();
                $keywords = [];
                if (isset($inputs['keywords'])) {
                    $keywords = is_string($inputs['keywords']) ? json_decode($inputs['keywords'], true) : $inputs['keywords'];
                }

                if ($request->hasFile('group_image')) {
                    $profileFolder = config('constant.group_image');
                    if (!Storage::exists($profileFolder)) {
                        Storage::makeDirectory($profileFolder);
                    }
                    $avatar = Storage::disk('s3')->putFile($profileFolder, $request->file('group_image'),'public');
                    $fileName = basename($avatar);
                    $avatar = $profileFolder . '/' . $fileName;
                }

                $data = [
                    'created_by' => $user->id,
                    'title' => $inputs['title'],
                    'image' => isset($avatar) ? $avatar : null,
                ];
                $group = GroupChatRoom::create($data);
                foreach ($keywords as $keyword) {
                    GroupChatRoomKeyword::create([
                        'group_id' => $group->id,
                        'keyword_id' => $keyword
                    ]);
                }

                DB::commit();
                return $this->sendSuccessResponse("Group chat room created successfully.", 200);

            }else{
                return $this->sendSuccessResponse(Lang::get('messages.user.token_expired'), 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

    public function listGroups(Request $request)
    {
        $user = Auth::user();
        $inputs = $request->all();
        $timezone = $inputs['timezone'] ?? "Asia/Seoul";

        try {
            if($user){
                DB::beginTransaction();

                if (!isset($inputs['search']) || $inputs['search']==""){
                    $my_group_chat = GroupChatRoom::leftjoin('group_chat_room_joined_users', function ($join) {
                            $join->on('group_chat_rooms.id', '=', 'group_chat_room_joined_users.group_chat_room_id');
                        })
                        ->join('users_detail','users_detail.user_id','group_chat_rooms.created_by')
                        ->whereNull('users_detail.deleted_at')
                        ->where(function($q) use ($user){
                            $q->where('group_chat_rooms.created_by', $user->id)
                                ->orWhere('group_chat_room_joined_users.user_id',$user->id);
                        })
                        ->select(
                            'group_chat_rooms.id as group_id',
                            'group_chat_rooms.title',
                            'users_detail.name',
                            'group_chat_rooms.image',
                            'group_chat_rooms.created_at',
                            DB::raw('1 as is_in_group'),
                            'group_chat_rooms.created_by as leader_id',
                            DB::raw("(CASE WHEN {$user->id} = group_chat_rooms.created_by THEN  1
                                    ELSE 0
                                    END) AS is_leader"),
                            DB::raw('IFNULL(group_chat_room_joined_users.created_at, group_chat_rooms.created_at) as joined_date')
                        )
                        ->get();
                    $my_group_chat->map(function ($value) use($timezone){
                        $created_at = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($value->created_at), "UTC")->setTimezone($timezone)->toDateTimeString();
                        $formattedDateTime = Carbon::parse($created_at)->format('F d,Y');
                        $value->created_date = $formattedDateTime;

                        $members = GroupChatRoomJoinedUser::where('group_chat_room_id',$value->group_id)->count();
                        $value->total_members = $members + 1;

                        return $value;
                    });
                    $my_group_chat_ids = collect($my_group_chat)->pluck('group_id')->toArray();
                    $last_joined = collect($my_group_chat)->sortByDesc('joined_date')->first();
                    $my_group_chat->makeHidden(['image','joined_date']);

                    if (isset($last_joined)){
                        $keywords = GroupChatRoomKeyword::join('keywords','keywords.id','group_chat_room_keywords.keyword_id')
                            ->where('group_chat_room_keywords.group_id',$last_joined->group_id)
                            ->orderBy('keywords.order','ASC')
                            ->pluck('keywords.value')->toArray();
                    }
                    $recommend_group_chat = GroupChatRoom::whereNotIn('group_chat_rooms.id',$my_group_chat_ids)
                        ->join('users_detail','users_detail.user_id','group_chat_rooms.created_by')
                        ->leftJoin('group_chat_room_joined_users', 'group_chat_room_joined_users.group_chat_room_id', '=', 'group_chat_rooms.id')
                        ->whereNull('users_detail.deleted_at')
                        ->select(
                            'group_chat_rooms.id as group_id',
                            'group_chat_rooms.title',
                            'users_detail.name',
                            'group_chat_rooms.image',
                            'group_chat_rooms.created_at',
                            DB::raw('0 as is_in_group'),
                            'group_chat_rooms.created_by as leader_id',
                            DB::raw("(CASE WHEN {$user->id} = group_chat_rooms.created_by THEN  1
                                    ELSE 0
                                    END) AS is_leader"),
                            DB::raw('COUNT(group_chat_room_joined_users.id)+1 as total_members')
                        )
                        ->groupBy('group_chat_rooms.id', 'group_chat_rooms.title', 'users_detail.name', 'group_chat_rooms.image', 'group_chat_rooms.created_at', 'group_chat_rooms.created_by')
                        ->orderBy('total_members','DESC')
                        ->get();
                    if (isset($last_joined) && isset($keywords)){
                        $recommend_group_chat = $recommend_group_chat->filter(function ($groupChat) use ($keywords) {
                            foreach ($keywords as $keyword) {
                                if (str_contains($groupChat->title, $keyword) !== false) {
                                    return true; // Keep the group chat in the filtered collection
                                }
                            }
                            return false; // Filter out the group chat if none of the keywords match
                        });
                    }

                    $recommend_group_chat->map(function ($value) use($timezone){
                        $created_at = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($value->created_at), "UTC")->setTimezone($timezone)->toDateTimeString();
                        $formattedDateTime = Carbon::parse($created_at)->format('F d,Y');
                        $value->created_date = $formattedDateTime;

//                        $members = GroupChatRoomJoinedUser::where('group_chat_room_id',$value->group_id)->count();
//                        $value->total_members = $members + 1;
                        return $value;
                    });
                    $recommend_group_chat->makeHidden(['image']);

                    $data['my_group_chat'] = $my_group_chat;
                    $data['recommend_group_chat'] = $recommend_group_chat;
                }
                else {
                    $search_data = GroupChatRoom::where('group_chat_rooms.title', 'LIKE', "%{$inputs['search']}%")
                        ->join('users_detail','users_detail.user_id','group_chat_rooms.created_by')
                        ->whereNull('users_detail.deleted_at')
                        ->select(
                            'group_chat_rooms.id as group_id',
                            'group_chat_rooms.title',
                            'users_detail.name',
                            'group_chat_rooms.image',
                            'group_chat_rooms.created_at',
                            'group_chat_rooms.created_by as leader_id',
                            DB::raw("(CASE WHEN {$user->id} = group_chat_rooms.created_by THEN  1
                                    ELSE 0
                                    END) AS is_leader")
                        )
                        ->get();
                    $search_data->map(function ($value) use($user,$timezone){
                        $is_in_group = 0;
                        if ($value->leader_id==$user->id){
                            $is_in_group = 1;
                        }
                        $joined_data = GroupChatRoomJoinedUser::where('user_id',$user->id)->where('group_chat_room_id',$value->id)->first();
                        if (!empty($joined_data)){
                            $is_in_group = 1;
                        }
                        $value->is_in_group = $is_in_group;

                        $created_at = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($value->created_at), "UTC")->setTimezone($timezone)->toDateTimeString();
                        $formattedDateTime = Carbon::parse($created_at)->format('F d,Y');
                        $value->created_date = $formattedDateTime;

                        $members = GroupChatRoomJoinedUser::where('group_chat_room_id',$value->group_id)->count();
                        $value->total_members = $members + 1;

                        return $value;
                    });
                    $search_data->makeHidden(['image']);

                    $data['my_group_chat'] = $search_data;
                    $data['recommend_group_chat'] = [];
                }

                DB::commit();
                return $this->sendSuccessResponse("Groups list get successfully.", 200, $data);

            }else{
                return $this->sendSuccessResponse(Lang::get('messages.user.token_expired'), 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

    public function infoGroup(Request $request)
    {
        $user = Auth::user();
        $inputs = $request->all();
        try {
            $validator = Validator::make($request->all(), [
                'group_id' => 'required',
            ], [], [
                'group_id' => 'Group ID',
            ]);
            if ($validator->fails()) {
                return $this->sendCustomErrorMessage($validator->errors()->toArray(), 422);
            }

            if($user){
                DB::beginTransaction();

                $info_group = GroupChatRoom::where('group_chat_rooms.id',$inputs['group_id'])
                    ->join('users_detail','users_detail.user_id','group_chat_rooms.created_by')
                    ->whereNull('users_detail.deleted_at')
                    ->select(
                        'group_chat_rooms.id as group_id',
                        'group_chat_rooms.title',
                        'users_detail.name',
                        'group_chat_rooms.image'
                    )
                    ->first();
                $info_group->makeHidden(['image']);

                DB::commit();
                return $this->sendSuccessResponse("Group chat room get successfully.", 200, $info_group);

            }else{
                return $this->sendSuccessResponse(Lang::get('messages.user.token_expired'), 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

    public function joinGroup(Request $request)
    {
        $user = Auth::user();
        $inputs = $request->all();
        try {
            $validator = Validator::make($request->all(), [
                'group_id' => 'required',
            ], [], [
                'group_id' => 'Group ID',
            ]);
            if ($validator->fails()) {
                return $this->sendCustomErrorMessage($validator->errors()->toArray(), 422);
            }

            if($user){
                DB::beginTransaction();

                $is_blocked = GroupBlockedUser::where('user_id',$user->id)->where('group_id',$inputs['group_id'])->first();
                if (!empty($is_blocked)){
                    return $this->sendFailedResponse("User is blocked by leader.", 402);
                }
                GroupChatRoomJoinedUser::create([
                    'group_chat_room_id' => $inputs['group_id'],
                    'user_id' => $user->id,
                ]);

                DB::commit();
                return $this->sendSuccessResponse("Group chat room joined successfully.", 200);
            }else{
                return $this->sendSuccessResponse(Lang::get('messages.user.token_expired'), 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

    public function unjoinGroup(Request $request)
    {
        DB::beginTransaction();
        $user = Auth::user();
        $inputs = $request->all();
        try {
            $validator = Validator::make($request->all(), [
                'group_id' => 'required',
            ], [], [
                'group_id' => 'Group ID',
            ]);
            if ($validator->fails()) {
                return $this->sendCustomErrorMessage($validator->errors()->toArray(), 422);
            }

            if($user){
                GroupChatRoomJoinedUser::where('user_id',$user->id)
                    ->where('group_chat_room_id',$inputs['group_id'])
                    ->delete();

                DB::commit();
                return $this->sendSuccessResponse("Group chat room un-joined successfully.", 200);
            }else{
                return $this->sendSuccessResponse(Lang::get('messages.user.token_expired'), 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

    public function groupMembers(Request $request)
    {
        DB::beginTransaction();
        $user = Auth::user();
        $inputs = $request->all();
        $timezone = $inputs['timezone'] ?? "Asia/Seoul";
        try {
            $validator = Validator::make($request->all(), [
                'group_id' => 'required',
            ], [], [
                'group_id' => 'Group ID',
            ]);
            if ($validator->fails()) {
                return $this->sendCustomErrorMessage($validator->errors()->toArray(), 422);
            }

            if($user){
                $joined_users = GroupChatRoomJoinedUser::where('group_chat_room_joined_users.group_chat_room_id',$inputs['group_id'])
                    ->join('users_detail','users_detail.user_id','group_chat_room_joined_users.user_id')
                    ->whereNull('users_detail.deleted_at')
                    ->select(
                        'users_detail.user_id',
                        'users_detail.name',
                        'users_detail.avatar',
                        DB::raw('0 as is_leader')
                    );

                $members = DB::table('group_chat_rooms')
                    ->where('group_chat_rooms.id',$inputs['group_id'])
                    ->join('users_detail','users_detail.user_id','group_chat_rooms.created_by')
                    ->whereNull('users_detail.deleted_at')
                    ->select(
                        'users_detail.user_id',
                        'users_detail.name',
                        'users_detail.avatar',
                        DB::raw('1 as is_leader')
                    )
                    ->limit(1)
                    ->union($joined_users)
                    ->get();

                $members->map(function ($value) {
                    if (empty($value->avatar)) {
                        $value->avatar = asset('img/avatar/avatar-1.png');
                    } else {
                        if (!filter_var($value->avatar, FILTER_VALIDATE_URL)) {
                            $value->avatar = Storage::disk('s3')->url($value->avatar);
                        } else {
                            $value->avatar = $value->avatar;
                        }
                    }

                    return $value;
                });

                $group_info = GroupChatRoom::where('group_chat_rooms.id',$inputs['group_id'])
                    ->join('users_detail','users_detail.user_id','group_chat_rooms.created_by')
                    ->whereNull('users_detail.deleted_at')
                    ->select([
                        'group_chat_rooms.id as group_id',
                        'group_chat_rooms.title',
                        'users_detail.name',
                        'group_chat_rooms.image',
                        'group_chat_rooms.created_at',
                        DB::raw('1 as is_in_group'),
                        'group_chat_rooms.created_by as leader_id',
                        DB::raw("(CASE WHEN {$user->id} = group_chat_rooms.created_by THEN  1
                                    ELSE 0
                                    END) AS is_leader")
                        ])
                    ->first();
                $group_info['keywords'] = GroupChatRoomKeyword::join('keywords','keywords.id','group_chat_room_keywords.keyword_id')
                    ->where('group_chat_room_keywords.group_id',$inputs['group_id'])
                    ->orderBy('keywords.order','ASC')
                    ->get(['keywords.id','keywords.value','keywords.order']);
                $group_info['total_members'] = count($members->toArray());
                $created_at = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($group_info->created_at), "UTC")->setTimezone($timezone)->toDateTimeString();
                $formattedDateTime = Carbon::parse($created_at)->format('F d,Y');
                $group_info['created_date'] = $formattedDateTime;
                $group_info->makeHidden(['image']);

                $data['group_info'] = $group_info;
                $data['members_list'] = $members;
                DB::commit();
                return $this->sendSuccessResponse("Group chat room members get successfully.", 200, $data);
            }else{
                return $this->sendSuccessResponse(Lang::get('messages.user.token_expired'), 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

    public function blockedUsers(Request $request)
    {
        DB::beginTransaction();
        $user = Auth::user();
        $inputs = $request->all();
        try {
            $validator = Validator::make($request->all(), [
                'group_id' => 'required',
            ], [], [
                'group_id' => 'Group ID',
            ]);
            if ($validator->fails()) {
                return $this->sendCustomErrorMessage($validator->errors()->toArray(), 422);
            }

            if($user){
                $blocked_users = GroupBlockedUser::where('group_id',$inputs['group_id'])
                    ->join('users_detail','users_detail.user_id','group_blocked_users.user_id')
                    ->whereNull('users_detail.deleted_at')
                    ->select(
                        'group_blocked_users.user_id',
                        'users_detail.name',
                        'users_detail.avatar'
                    )
                    ->get();
                $blocked_users->map(function ($value) {
                    if (empty($value->avatar)) {
                        $value->avatar = asset('img/avatar/avatar-1.png');
                    } else {
                        if (!filter_var($value->avatar, FILTER_VALIDATE_URL)) {
                            $value->avatar = Storage::disk('s3')->url($value->avatar);
                        } else {
                            $value->avatar = $value->avatar;
                        }
                    }

                    return $value;
                });

                DB::commit();
                return $this->sendSuccessResponse("Blocked users list.", 200, $blocked_users);
            }else{
                return $this->sendSuccessResponse(Lang::get('messages.user.token_expired'), 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

    public function unblockUser(Request $request)
    {
        DB::beginTransaction();
        $user = Auth::user();
        $inputs = $request->all();
        try {
            $validator = Validator::make($request->all(), [
                'group_id' => 'required',
                'user_id' => 'required',
            ], [], [
                'group_id' => 'Group ID',
            ]);
            if ($validator->fails()) {
                return $this->sendCustomErrorMessage($validator->errors()->toArray(), 422);
            }

            if($user){
                GroupBlockedUser::where('group_id',$inputs['group_id'])->where('user_id',$inputs['user_id'])->delete();

                DB::commit();
                return $this->sendSuccessResponse("User un-blocked successfully.", 200);
            }else{
                return $this->sendSuccessResponse(Lang::get('messages.user.token_expired'), 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

    public function editGroupInfo(Request $request)
    {
        DB::beginTransaction();
        $user = Auth::user();
        $inputs = $request->all();
        try {
            $validator = Validator::make($request->all(), [
                'group_id' => 'required',
                'group_image' => 'image|mimes:jpeg,jpg,gif,svg,png',
            ], [], [
                'group_id' => 'Group ID',
            ]);
            if ($validator->fails()) {
                return $this->sendCustomErrorMessage($validator->errors()->toArray(), 422);
            }

            if($user){
                if (isset($inputs['title'])){
                    GroupChatRoom::where('id',$inputs['group_id'])->update([
                        'title' => $inputs['title'],
                    ]);
                }

                if ($request->hasFile('group_image')) {
                    $group_data = GroupChatRoom::where('id',$inputs['group_id'])->first();
                    Storage::disk('s3')->delete($group_data->image);

                    $profileFolder = config('constant.group_image');
                    if (!Storage::exists($profileFolder)) {
                        Storage::makeDirectory($profileFolder);
                    }
                    $avatar = Storage::disk('s3')->putFile($profileFolder, $request->file('group_image'),'public');
                    $fileName = basename($avatar);
                    $avatar = $profileFolder . '/' . $fileName;

                    GroupChatRoom::where('id',$inputs['group_id'])->update([
                        'image' => $avatar
                    ]);
                }

                if (isset($inputs['keywords'])){
                    $keywords = is_string($inputs['keywords']) ? json_decode($inputs['keywords'], true) : $inputs['keywords'];
                    foreach ($keywords as $keyword) {
                        GroupChatRoomKeyword::firstOrCreate([
                            'group_id' => $inputs['group_id'],
                            'keyword_id' => $keyword
                        ]);
                    }
                    GroupChatRoomKeyword::where('group_id',$inputs['group_id'])->whereNotIn('keyword_id',$keywords)->delete();
                }

                DB::commit();
                return $this->sendSuccessResponse("Group updated successfully.", 200);
            }else{
                return $this->sendSuccessResponse(Lang::get('messages.user.token_expired'), 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

    public function giveLeader(Request $request)
    {
        DB::beginTransaction();
        $user = Auth::user();
        $inputs = $request->all();
        try {
            $validator = Validator::make($request->all(), [
                'group_id' => 'required',
                'user_id' => 'required',
            ], [], [
                'group_id' => 'Group ID',
            ]);
            if ($validator->fails()) {
                return $this->sendCustomErrorMessage($validator->errors()->toArray(), 422);
            }

            if($user){
                GroupChatRoomJoinedUser::where('group_chat_room_id',$inputs['group_id'])->where('user_id',$inputs['user_id'])->delete();
                GroupChatRoom::where('id',$inputs['group_id'])->update([
                   'created_by' => $inputs['user_id']
                ]);
                GroupChatRoomJoinedUser::create([
                   'group_chat_room_id' => $inputs['group_id'],
                   'user_id' => $user->id
                ]);

                DB::commit();
                return $this->sendSuccessResponse("Leader authority assigned successfully.", 200);
            }else{
                return $this->sendSuccessResponse(Lang::get('messages.user.token_expired'), 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

    public function keywordList(Request $request)
    {
        DB::beginTransaction();
        $user = Auth::user();
        $inputs = $request->all();
        try {
            if($user){
                $keywords = Keyword::orderBy('order','ASC')->get(['id','value','order']);

                DB::commit();
                return $this->sendSuccessResponse("keywords get successfully.", 200, $keywords);
            }else{
                return $this->sendSuccessResponse(Lang::get('messages.user.token_expired'), 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

    public function setPush(Request $request){
        DB::beginTransaction();
        $user = Auth::user();
        $inputs = $request->all();
        try {
            if($user){
                $push = ($inputs['push_notification']==1) ? "on" : "off";
                GroupChatRoom::where('id',$inputs['group_id'])->where('created_by',$user->id)->update([
                   "push_notification" => $push
                ]);
                GroupChatRoomJoinedUser::where('group_chat_room_id',$inputs['group_id'])->where('user_id',$user->id)->update([
                    "push_notification" => $push
                ]);

                DB::commit();
                return $this->sendSuccessResponse("Push notification updated successfully.", 200);
            }else{
                return $this->sendSuccessResponse(Lang::get('messages.user.token_expired'), 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

    public function reportUser(Request $request){
        DB::beginTransaction();
        $user = Auth::user();
        $inputs = $request->all();
        try {
            if($user){
                ReportedUser::create([
                    'reporter_user_id' => $user->id,
                    'reported_user_id' => $inputs['user_id'],
                    'reason' => $inputs['reason']
                ]);

                DB::commit();
                return $this->sendSuccessResponse("User reported successfully.", 200);
            }else{
                return $this->sendSuccessResponse(Lang::get('messages.user.token_expired'), 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

}
