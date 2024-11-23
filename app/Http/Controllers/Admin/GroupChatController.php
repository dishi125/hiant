<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GroupChatRoom;
use App\Models\GroupChatRoomJoinedUser;
use App\Models\GroupChatRoomMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GroupChatController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Group chat list';

        return view('admin.group-chat.index', compact('title'));
    }

    public function getJsonData(Request $request){
        $columns = array(
            0 => 'group_chat_rooms.title',
            1 => 'users_detail.name',
            2 => 'group_chat_rooms.created_at',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');
        $draw = $request->input('draw');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $adminTimezone = $this->getAdminUserTimezone();

        try {
            $data = [];
            $query = GroupChatRoom::join('users_detail','users_detail.user_id','group_chat_rooms.created_by')
                    ->whereNull('users_detail.deleted_at')
                    ->select(
                        'group_chat_rooms.id as group_id',
                        'group_chat_rooms.title',
                        'users_detail.name',
                        'group_chat_rooms.image',
                        'group_chat_rooms.created_at',
                        'group_chat_rooms.created_by as leader_id'
                    );

            $totalData = count($query->get());
            $totalFiltered = $totalData;

            if (!empty($search)) {
                $query = $query->where(function($q) use ($search){
                    $q->where('group_chat_rooms.title', 'LIKE', "%{$search}%")
                        ->orWhere('users_detail.name', 'LIKE', "%{$search}%");
                });

                $totalFiltered = $query->count();
            }

            $result = $query->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $count = 0;
            foreach($result as $res){
                $data[$count]['group_title'] = $res->title;
                $data[$count]['leader'] = $res->name;
                $data[$count]['created_at'] = $this->formatDateTimeCountryWise($res->created_at, $adminTimezone);

                $members = GroupChatRoomJoinedUser::where('group_chat_room_id',$res->group_id)->count();
                $data[$count]['participants'] = $members + 1;
                $data[$count]['see_more'] = '<a role="button" href="javascript:void(0)" onclick="viewEntireChat('.$res->group_id.')" title="" data-original-title="View More" class="mx-1 btn btn-primary btn-sm" data-toggle="tooltip">See More</a>';

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

    public function showMessages($id){
        $adminTimezone = $this->getAdminUserTimezone();
        $group_chat_messages = DB::table('group_chat_room_messages')
                            ->where('group_id',$id)
                            ->join('users_detail','users_detail.user_id','group_chat_room_messages.from_user_id')
                            ->whereNull('users_detail.deleted_at')
                            ->select([
                                'group_chat_room_messages.id',
                                'group_chat_room_messages.group_id',
                                'group_chat_room_messages.type',
                                'group_chat_room_messages.message',
                                'group_chat_room_messages.created_at',
                                'users_detail.name'
                                ])
                            ->get();

        return view('admin.group-chat.show-messages-popup', compact('group_chat_messages','adminTimezone'));
    }

}
