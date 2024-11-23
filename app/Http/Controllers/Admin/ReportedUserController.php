<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportedUser;
use App\Models\UserDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportedUserController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Reported user list';

        return view('admin.reported-users.index', compact('title'));
    }

    public function getJsonData(Request $request){
        $columns = array(
            0 => 'reported_user_id',
            1 => 'reason',
            2 => 'reporter_user_id',
            3 => 'created_at',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');
        $draw = $request->input('draw');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

//        try {
        $data = [];
        $query = ReportedUser::query();

        $totalData = count($query->get());
        $totalFiltered = $totalData;

        if (!empty($search)) {
            $query = $query->where(function($q) use ($search){
                $q->where('reason', 'LIKE', "%{$search}%");
            });

            $totalFiltered = $query->count();
        }

        $result = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $count = 0;
        foreach($result as $res){
            $reported_user = UserDetail::where('user_id',$res->reported_user_id)->pluck('name')->first();
            $data[$count]['reported_user'] = $reported_user;
            $data[$count]['reason'] = $res->reason;

            $reported_by = UserDetail::where('user_id',$res->reporter_user_id)->pluck('name')->first();
            $data[$count]['reported_by'] = $reported_by;
            $data[$count]['time'] = Carbon::parse($res->created_at)->format('Y-m-d H:i:s');

            $count++;
        }

        $jsonData = array(
            "draw" => intval($draw),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );
        return response()->json($jsonData);
        /*} catch (\Exception $ex) {
            Log::info($ex);
            $jsonData = array(
                "draw" => intval($draw),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            );
            return response()->json($jsonData);
        }*/
    }

}
