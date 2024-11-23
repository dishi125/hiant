<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Keyword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class KeywordController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Keyword List';

        return view('admin.keyword.index', compact('title'));
    }

    public function addKeyword(Request $request){
        $title = 'Add Keyword';
        return view('admin.keyword.form', compact('title'));
    }

    public function storeKeyword(Request $request)
    {
        try {
            Log::info('Start code for the add keyword');
            DB::beginTransaction();
            $inputs = $request->all();

            $validator = Validator::make($request->all(), [
                'value' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            Keyword::create([
                'value' => $inputs['value'],
            ]);

            Log::info('End code for the add keyword');
            DB::commit();
            notify()->success("keyword added successfully", "Success", "topRight");

            $redirect = 'admin.keyword.index';
            return redirect()->route($redirect);
        } catch (\Exception $e) {
            Log::info('Exception in the add keyword.');
            Log::info($e);
            notify()->error("Failed to add keyword", "Error", "topRight");
            return redirect()->route('admin.keyword.index');
        }
    }

    public function getJsonData(Request $request){
        $columns = array(
            0 => 'value',
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
            $query = Keyword::query();

            $totalData = count($query->get());
            $totalFiltered = $totalData;

            if (!empty($search)) {
                $query = $query->where(function($q) use ($search){
                    $q->where('value', 'LIKE', "%{$search}%");
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
                $data[$count]['keyword'] = $res->value;
                $data[$count]['order'] = $res->order;

                $editButton =  "<a role='button' href='" . route('admin.keyword.edit', $res->id) . "' title='' data-original-title='Edit' class='btn btn-primary' data-toggle='tooltip'><i class='fa fa-edit'></i></a>";
                $data[$count]['action'] = $editButton;
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
                    $isUpdate = Keyword::where('id',$value['id'])->update(['order' => $value['position']]);
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

    public function editKeyword($id){
        $title = 'Edit Keyword';
        $keyword = Keyword::find($id);

        return view('admin.keyword.form', compact('title','keyword'));
    }

    public function updateKeyword(Request $request,$id)
    {
        try {
            Log::info('Start code for the edit keyword');
            DB::beginTransaction();
            $inputs = $request->all();

            $validator = Validator::make($request->all(), [
                'value' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            Keyword::updateOrCreate([
                'id' => $id,
            ],[
                'value' => $inputs['value'],
            ]);

            Log::info('End code for the edit keyword');
            DB::commit();
            notify()->success("Keyword updated successfully", "Success", "topRight");

            $redirect = 'admin.keyword.index';
            return redirect()->route($redirect);
        } catch (\Exception $e) {
            Log::info('Exception in the edit keyword.');
            Log::info($e);
            notify()->error("Failed to edit keyword", "Error", "topRight");
            return redirect()->route('admin.keyword.index');
        }
    }

}
