<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Category List';

        return view('admin.category.index', compact('title'));
    }

    public function getJsonData(Request $request){
        $columns = array(
            0 => 'category',
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
            $query = Category::query();

            $totalData = count($query->get());
            $totalFiltered = $totalData;

            if (!empty($search)) {
                $query = $query->where(function($q) use ($search){
                    $q->where('category', 'LIKE', "%{$search}%");
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
                $data[$count]['category'] = $res->category;
                $data[$count]['order'] = $res->order;

                $editButton =  "<a role='button' href='" . route('admin.category.edit', $res->id) . "' title='' data-original-title='Edit' class='btn btn-primary' data-toggle='tooltip'><i class='fa fa-edit'></i></a>";
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
                    $isUpdate = Category::where('id',$value['id'])->update(['order' => $value['position']]);
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

    public function addCategory(Request $request){
        $title = 'Add Category';
        return view('admin.category.form', compact('title'));
    }

    public function storeCategory(Request $request)
    {
        try {
            Log::info('Start code for the add category');
            DB::beginTransaction();
            $inputs = $request->all();

            $validator = Validator::make($request->all(), [
                'category' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            Category::create([
                'category' => $inputs['category'],
            ]);

            Log::info('End code for the add category');
            DB::commit();
            notify()->success("category added successfully", "Success", "topRight");

            $redirect = 'admin.category.index';
            return redirect()->route($redirect);
        } catch (\Exception $e) {
            Log::info('Exception in the add category.');
            Log::info($e);
            notify()->error("Failed to add category", "Error", "topRight");
            return redirect()->route('admin.category.index');
        }
    }

    public function editCategory($id){
        $title = 'Edit Category';
        $category = Category::find($id);

        return view('admin.category.form', compact('title','category'));
    }

    public function updateCategory(Request $request,$id)
    {
        try {
            Log::info('Start code for the edit category');
            DB::beginTransaction();
            $inputs = $request->all();

            $validator = Validator::make($request->all(), [
                'category' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            Category::updateOrCreate([
                'id' => $id,
            ],[
                'category' => $inputs['category'],
            ]);

            Log::info('End code for the edit category');
            DB::commit();
            notify()->success("Category updated successfully", "Success", "topRight");

            $redirect = 'admin.category.index';
            return redirect()->route($redirect);
        } catch (\Exception $e) {
            Log::info('Exception in the edit category.');
            Log::info($e);
            notify()->error("Failed to edit category", "Error", "topRight");
            return redirect()->route('admin.category.index');
        }
    }

}
