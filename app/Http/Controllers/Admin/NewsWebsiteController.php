<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsWebsite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewsWebsiteController extends Controller
{
    public function index(Request $request)
    {
        $title = 'News website List';

        return view('admin.news-websites.index', compact('title'));
    }

    public function getJsonData(Request $request){
        $columns = array(
            0 => 'website_name',
            1 => 'order',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');
        $draw = $request->input('draw');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        try {
            $data = [];
            $query = NewsWebsite::query();

            $totalData = count($query->get());
            $totalFiltered = $totalData;

            if (!empty($search)) {
                $query = $query->where(function($q) use ($search){
                    $q->where('website_name', 'LIKE', "%{$search}%");
                });

                $totalFiltered = $query->count();
            }

            $result = $query->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $count = 0;
            foreach($result as $res){
                $data[$count]['id'] = $res->id;
                $data[$count]['website'] = $res->website_name." (".$res->type.")";
                $data[$count]['order'] = $res->order;

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

    public function updateOrder(Request $request){
        $inputs = $request->all();
        try{
            $order = $inputs['order'] ?? '';
            if(!empty($order)){
                foreach($order as $value){
                    $isUpdate = NewsWebsite::where('id',$value['id'])->update(['order' => $value['position']]);
                }
            }
            return response()->json([
                'success' => true
            ]);
        }catch (\Exception $ex) {
            Log::info($ex);
            return response()->json([
                'success' => false
            ]);
        }
    }

}
