<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Community;
use App\Models\CommunityCommentLikes;
use App\Models\CommunityCommentReply;
use App\Models\CommunityCommentReplyLikes;
use App\Models\CommunityComments;
use App\Models\CommunityLikes;
use App\Models\CompletedCustomer;
use App\Models\DeleteAccountReason;
use App\Models\EntityTypes;
use App\Models\GroupBlockedUser;
use App\Models\GroupChatRoom;
use App\Models\GroupChatRoomJoinedUser;
use App\Models\GroupChatRoomKeyword;
use App\Models\GroupChatRoomMessage;
use App\Models\GroupChatRoomMessageFile;
use App\Models\GroupChatRoomUnreadMessage;
use App\Models\Hospital;
use App\Models\LikedGroupChatRoomMessage;
use App\Models\ManagerActivityLogs;
use App\Models\Message;
use App\Models\MessageNotificationStatus;
use App\Models\Notice;
use App\Models\Post;
use App\Models\PrivateChatMessage;
use App\Models\ReloadCoinRequest;
use App\Models\ReportClient;
use App\Models\RequestedCustomer;
use App\Models\RequestForm;
use App\Models\ReviewCommentLikes;
use App\Models\ReviewCommentReply;
use App\Models\ReviewCommentReplyLikes;
use App\Models\ReviewComments;
use App\Models\ReviewLikes;
use App\Models\Reviews;
use App\Models\SearchHistory;
use App\Models\Shop;
use App\Models\ShopFollowers;
use App\Models\User;
use App\Models\UserBlockHistory;
use App\Models\UserCredit;
use App\Models\UserCreditHistory;
use App\Models\UserDetail;
use App\Models\UserDevices;
use App\Models\UserEntityRelation;
use App\Models\UserHidePopupImage;
use App\Models\UserInstagramHistory;
use Cookie;
use Carbon\Carbon;
use App\Util\Firebase;
use App\Traits\ResponseTrait;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ResponseTrait;

    protected $firebase;
    public function __construct()
    {
        $this->firebase = new Firebase();
    }

    public function getAdminUserTimezone(){

        $timezone = isset($_COOKIE['admin_timezone_new']) ? $_COOKIE['admin_timezone_new'] : '';

        if(empty($timezone)){
            Log::info('Admin Timezone Function.');
            $timezone = '';
            $ip     = $this->getIPAddress();
            //$ip     = '182.70.126.26';
            $json   = file_get_contents( 'http://ip-api.com/json/' . $ip);
            $ipData = json_decode( $json, true);
            Log::info(Carbon::now()->format('Y-m-d H:i:s'));
            Log::info($ip);
            Log::info($json);
            if (!empty($ipData) && !empty($ipData['timezone'])) {
                $timezone = $ipData['timezone'];
                $countryCode = $ipData['countryCode'];
            } else {
                $timezone = 'UTC';
                $countryCode = 'KR';
            }
            setcookie('admin_timezone_new',$timezone,time()+60*60*24*365, '/');
            setcookie('admin_country_code',$countryCode,time()+60*60*24*365, '/');
            Log::info($timezone);
            return $timezone;
        }else{
            return $timezone;
        }
    }

    public function getIPAddress() {
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /* Format DateTime Country wise */
    public static function formatDateTimeCountryWise($date,$adminTimezone,$format='Y-m-d H:i:s'){
        if(empty($date)) return;
        $dateShow = Carbon::createFromFormat('Y-m-d H:i:s',Carbon::parse($date), "UTC")->setTimezone($adminTimezone)->toDateTimeString();
        return Carbon::parse($dateShow)->format($format);
    }

    //convert date time from milliseconds time
    public static function formatDateTimeCountryWiseFromMs($timestampInMillis,$Timezone,$format='Y-m-d H:i:s'){
        if(empty($timestampInMillis)) return;
        $timestampInSeconds = $timestampInMillis / 1000;
        $carbon = Carbon::createFromTimestamp($timestampInSeconds, 'UTC');
        $carbon->setTimezone($Timezone);
        $formattedDateTime = $carbon->format($format);

        return $formattedDateTime;
    }

    public function deleteUserDetails(Request $request){
        $inputs = $request->all();
        $userID = $inputs['userId'];
        try {
            DB::beginTransaction();

            if(!empty($userID)){
                DeleteAccountReason::where('user_id', $userID)->update(['is_deleted_user' => 1]);

                UserDevices::where('user_id',$userID)->delete();
                UserEntityRelation::where('user_id',$userID)->delete();
                UserDetail::where('user_id',$userID)->delete();
                User::where('id',$userID)->delete();

                $groups = GroupChatRoom::where('created_by',$userID)->get();
                foreach ($groups as $group){
                    GroupChatRoomJoinedUser::where('group_chat_room_id',$group->id)->delete();
                    GroupBlockedUser::where('group_id',$group->id)->delete();
                    GroupChatRoomMessageFile::where('group_id',$group->id)->delete();
                    GroupChatRoomMessage::where('group_id',$group->id)->delete();
                    GroupChatRoomUnreadMessage::where('group_id',$group->id)->delete();
                    LikedGroupChatRoomMessage::where('group_id',$group->id)->delete();
                    GroupChatRoomKeyword::where('group_id',$group->id)->delete();
                    GroupChatRoom::where('id',$group->id)->delete();
                    //delete image
                    Storage::disk('s3')->delete($group->image);
                }
                GroupChatRoomJoinedUser::where('user_id',$userID)->delete();
                GroupBlockedUser::where('user_id',$userID)->delete();
                GroupChatRoomUnreadMessage::where('user_id',$userID)->delete();

                PrivateChatMessage::where('from_user_id',$userID)->delete();
                PrivateChatMessage::where('to_user_id',$userID)->delete();

                DB::commit();
                Log::info('Delete user code end.');
                return $this->sendSuccessResponse('User deleted successfully.', 200);
            }else{
                return $this->sendSuccessResponse('Failed to delete user.', 201);
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::info('Delete user code exception.');
            Log::info($ex);
            return $this->sendFailedResponse('Failed to delete user.', 201);
        }
    }

}
