<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeleteAccountReason;
use App\Models\EntityTypes;
use App\Models\GroupBlockedUser;
use App\Models\GroupChatRoom;
use App\Models\GroupChatRoomJoinedUser;
use App\Models\GroupChatRoomKeyword;
use App\Models\GroupChatRoomMessage;
use App\Models\GroupChatRoomMessageFile;
use App\Models\GroupChatRoomUnreadMessage;
use App\Models\LikedGroupChatRoomMessage;
use App\Models\PrivateChatMessage;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserDevices;
use App\Models\UserEntityRelation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeleteAccountReasonController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Reasons List';
        DeleteAccountReason::where('is_admin_read', 1)->update(['is_admin_read' => 0]);

        return view('admin.reasons-delete-account.index', compact('title'));
    }

    public function getJsonData(Request $request){
        $columns = array(
            0 => 'users_detail.name',
            1 => 'users_detail.mobile',
            2 => 'delete_account_reasons.reason',
            3 => 'delete_account_reasons.created_at',
            4 => 'users.created_at',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');
        $draw = $request->input('draw');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        try {
            $data = [];
            $query = DeleteAccountReason::leftjoin('users', 'users.id', 'delete_account_reasons.user_id')
                ->leftjoin('users_detail','users_detail.user_id','delete_account_reasons.user_id')
                ->select(
                    'delete_account_reasons.*',
                    'users_detail.name as user_name',
                    'users_detail.mobile as mobile_number',
                    'users.created_at as signup_date'
                );

            $totalData = count($query->get());
            $totalFiltered = $totalData;

            if (!empty($search)) {
                $query = $query->where(function($q) use ($search){
                    $q->where('users_detail.name', 'LIKE', "%{$search}%")
                        ->orWhere('delete_account_reasons.reason', 'LIKE', "%{$search}%");
                });

                $totalFiltered = $query->count();
            }

            $result = $query->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $count = 0;
            foreach($result as $res){
                $status = "";
                if ($res->is_deleted_user==1){
                    $status .= "Deleted";
                }
                else {
                    $status .= '<a href="javascript:void(0)" role="button" onclick="removeUser('.$res->id.')" class="btn btn-danger" data-toggle="tooltip" data-original-title="Delete"><i class="fa fa-trash"></i></a>';
                }

                $data[$count]['username'] = $res->user_name;
                $data[$count]['phone'] = $res->mobile_number;
                $data[$count]['reason'] = $res->reason;
                $data[$count]['request_date'] = Carbon::parse($res->created_at)->format('Y-m-d H:i:s');
                $data[$count]['signup_date'] = Carbon::parse($res->signup_date)->format('Y-m-d H:i:s');
                $data[$count]['status'] = $status;

                $count++;
            }

            $jsonData = array(
                "draw" => intval($draw),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );
            return response()->json($jsonData);
        } catch (\Exception $ex) {
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

    public function delete($id)
    {
        return view('admin.reasons-delete-account.delete', compact('id'));
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $delete_account_reason = DeleteAccountReason::where('id', $id)->first();
            $userID = $delete_account_reason->user_id;

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
            notify()->success("User deleted successfully", "Success", "topRight");
            return redirect()->route('admin.reasons-delete-account.index');
        } catch (\Exception $ex) {
            DB::rollBack();
            notify()->error("Failed to delete user", "Error", "topRight");
            return redirect()->route('admin.reasons-delete-account.index');
        }
    }

}
