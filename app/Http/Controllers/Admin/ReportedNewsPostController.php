<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportedNewsPost;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportedNewsPostController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Reported news post list';

        return view('admin.reported-post.index', compact('title'));
    }

    public function getJsonData(Request $request){
        $columns = array(
            0 => 'reported_news_posts.link',
            1 => 'reported_news_posts.reason',
            2 => 'users_detail.name',
            3 => 'reported_news_posts.created_at',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');
        $draw = $request->input('draw');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        try {
            $data = [];
            $query = ReportedNewsPost::leftjoin('users_detail','users_detail.user_id','reported_news_posts.user_id')
                ->where('reported_news_posts.is_blocked',0)
                ->select(
                    'reported_news_posts.*',
                    'users_detail.name as user_name'
                );

            $totalData = count($query->get());
            $totalFiltered = $totalData;

            if (!empty($search)) {
                $query = $query->where(function($q) use ($search){
                    $q->where('users_detail.name', 'LIKE', "%{$search}%")
                        ->orWhere('reported_news_posts.reason', 'LIKE', "%{$search}%")
                        ->orWhere('reported_news_posts.link', 'LIKE', "%{$search}%");
                });

                $totalFiltered = $query->count();
            }

            $result = $query->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $count = 0;
            foreach($result as $res){
                $data[$count]['post_link'] = $res->link;
                $data[$count]['reason'] = $res->reason;
                $data[$count]['user_name'] = $res->user_name;
                $data[$count]['time'] = Carbon::parse($res->created_at)->format('Y-m-d H:i:s');
                $data[$count]['action'] = '<a role="button" href="javascript:void(0)" onclick="reportPost('.$res->id.')" title="" data-original-title="Block post" class="mx-1 btn btn-primary btn-sm" data-toggle="tooltip">Block</a>';

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

    public function getPost($id)
    {
        return view('admin.reported-post.delete-post', compact('id'));
    }

    public function blockPost(Request $request){
        $inputs = $request->all();
        $news_post_id = $inputs['news_post_id'];
        try {
            DB::beginTransaction();

            if(!empty($news_post_id)){
                $news_post = ReportedNewsPost::where('id',$news_post_id)->first();
                ReportedNewsPost::where('type',$news_post['type'])
                    ->where('website_name',$news_post['website_name'])
                    ->where('link',$news_post['link'])
                    ->update([
                        'is_blocked' => 1
                    ]);

                DB::commit();
                Log::info('Block post code end.');
                return $this->sendSuccessResponse('News post blocked successfully.', 200);
            }else{
                return $this->sendSuccessResponse('Failed to block news post.', 201);
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::info('Block post code exception.');
            Log::info($ex);
            return $this->sendFailedResponse('Failed to block news post.', 201);
        }
    }
}
