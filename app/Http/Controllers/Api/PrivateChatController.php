<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrivateChatMessage;
use App\Models\PrivateChatPushNotification;
use App\Models\Status;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

class PrivateChatController extends Controller
{
    public function listChat(Request $request){
        $user = Auth::user();
        $inputs = $request->all();

        $notInUsers = DB::table('users')->where('id', '!=', $user->id)
            ->where(function ($q) {
                $q->where('status_id', '!=', Status::ACTIVE)->orWhere('chat_status', '!=', 1);
            })->pluck('id');

        $concatQuery = 'CASE
                            WHEN private_chat_messages.from_user_id = '.$user->id.' THEN CONCAT(private_chat_messages.from_user_id, "_", private_chat_messages.to_user_id)
                            ELSE CONCAT(private_chat_messages.to_user_id, "_", private_chat_messages.from_user_id)
                        END';
        $chatQuery = PrivateChatMessage::whereRaw("private_chat_messages.type='text' and (private_chat_messages.from_user_id = " . $user->id . " OR private_chat_messages.to_user_id = " . $user->id . ")")
            ->select(DB::raw('max(private_chat_messages.id) as message_id'))
            ->selectRaw("{$concatQuery} AS uniqe_records")
            ->groupBy('uniqe_records')
            ->pluck('message_id');
        $user_chat_list = PrivateChatMessage::whereRaw("private_chat_messages.type='text' and (private_chat_messages.from_user_id = " . $user->id . " OR private_chat_messages.to_user_id = " . $user->id . ")");
        if (isset($inputs['message_id']) && $inputs['message_id'] != '') {
            $user_chat_list = $user_chat_list->where("private_chat_messages.id", $inputs['message_id']);
        } else {
            $user_chat_list = $user_chat_list->whereIn('private_chat_messages.id', $chatQuery);
        }
        $user_chat_list = $user_chat_list->select(
            'private_chat_messages.*'
            )
            ->orderBy('private_chat_messages.id', 'DESC')
            ->orderBy(DB::raw("DATE_FORMAT(FROM_UNIXTIME(private_chat_messages.created_at/1000), '%Y-%m-%d %H:%i:%s')"),'DESC')
            ->paginate(config('constant.pagination_count'), "*", "private_chat_list_page");

        $i = 0;
        if (!empty($user_chat_list)) {
            foreach ($user_chat_list as $value) {
                $unread_count = DB::table('private_chat_messages')->where('status', 0)
                    ->where('to_user_id', $user->id)
                    ->where(function ($q) use ($value) {
                        $q->where('from_user_id', $value['from_user_id'])
                            ->orWhere('from_user_id', $value['to_user_id']);
                    })
                    ->count();
                $user_chat_list[$i]['count'] = $unread_count;

                $seconds = $value->created_at / 1000;
                $created_date = date("Y-m-d H:i:s", $seconds);
                $language_id = 4;
//                $user_chat_list[$i]['time_difference'] = $value ? timeAgo($created_date, $language_id, $inputs['timezone'])  : "";
                $user_chat_list[$i]['time'] = $value ? $this->formatDateTimeCountryWiseFromMs($value->created_at,$inputs['timezone'],"H:i")  : "";

                $this_user = ($user->id!=$value['from_user_id']) ? $value['from_user_id'] : $value['to_user_id'];
                $this_user = UserDetail::where('user_id',$this_user)->select(['name','avatar','user_id'])->first();
                $user_chat_list[$i]['user_name'] = $this_user->name;
                $user_chat_list[$i]['avatar'] = $this_user->avatar;
                $user_chat_list[$i]['user_id'] = $this_user->user_id;

                $i++;
            }
        }

        return $this->sendSuccessResponse("Private chat list.", 200, $user_chat_list);
    }

    public function setPush(Request $request){
        DB::beginTransaction();
        $user = Auth::user();
        $inputs = $request->all();
        try {
            if($user){
                $push = ($inputs['push_notification']==1) ? "on" : "off";
                PrivateChatPushNotification::where('user_id',$user->id)->where('from_user_id',$inputs['user_id'])->update([
                   'push' => $push
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

}
