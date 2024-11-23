<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoryPostController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Post List';

        return view('admin.category-posts.index', compact('title'));
    }

    public function addCategoryPost(Request $request){
        $title = 'Add Post';
        $categories = Category::orderBy('order','ASC')->get();

        return view('admin.category-posts.form', compact('title','categories'));
    }

    public function storeCategoryPost(Request $request)
    {
        try {
            Log::info('Start code for the add post');
            DB::beginTransaction();
            $inputs = $request->all();

            $validator = Validator::make($request->all(), [
                'category' => 'required',
                'title' => 'required',
                'agency_name' => 'required',
                'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg',
                'subcript' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            if ($request->hasFile('image')) {
                $categoryFolder = config('constant.category').'/'.$inputs['category'];
                if (!Storage::exists($categoryFolder)) {
                    Storage::makeDirectory($categoryFolder);
                }
                $image = Storage::disk('s3')->putFile($categoryFolder, $request->file('image'),'public');
                $fileName = basename($image);
                $image_path = $categoryFolder . '/' . $fileName;
            }
            CategoryPost::create([
                'category_id' => $inputs['category'],
                'title' => $inputs['title'],
                'agency_name' => $inputs['agency_name'],
                'image' => $image_path ?? null,
                'subcript' => $inputs['subcript'],
            ]);

            Log::info('End code for the add post');
            DB::commit();
            notify()->success("Post added successfully", "Success", "topRight");

            $redirect = 'admin.category-posts.index';
            return redirect()->route($redirect);
        } catch (\Exception $e) {
            Log::info('Exception in the add post.');
            Log::info($e);
            notify()->error("Failed to add post", "Error", "topRight");
            return redirect()->route('admin.category-posts.index');
        }
    }

    public function getJsonData(Request $request){
        $columns = array(
            0 => 'category_posts.title',
            1 => 'category_posts.agency_name',
            2 => 'categories.category',
            4 => 'category_posts.subcript',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');
        $draw = $request->input('draw');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        try {
            $data = [];
            $query = CategoryPost::join('categories','categories.id','category_posts.category_id')
                    ->select('category_posts.*','categories.category');

            $totalData = count($query->get());
            $totalFiltered = $totalData;

            if (!empty($search)) {
                $query = $query->where(function($q) use ($search){
                    $q->where('category_posts.title', 'LIKE', "%{$search}%");
                    $q->orWhere('category_posts.agency_name', 'LIKE', "%{$search}%");
                    $q->orWhere('category_posts.subcript', 'LIKE', "%{$search}%");
                    $q->orWhere('categories.category', 'LIKE', "%{$search}%");
                });

                $totalFiltered = $query->count();
            }

            $result = $query->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $count = 0;
            foreach($result as $res){
                $checked = $res->is_pin_to_top ? 'checked' : '';
                $pin_to_top = '<input type="checkbox" class="toggle-btn pintotop-toggle-btn" '.$checked.' data-id="'.$res->id.'" data-toggle="toggle" data-height="20" data-size="sm" data-onstyle="success" data-offstyle="danger">';

                $data[$count]['title'] = $res->title;
                $data[$count]['agency'] = $res->agency_name;
                $data[$count]['category'] = $res->category;
                $data[$count]['image'] = "<img src='$res->image' width='100px' height='100px' alt='Post'>";
                $data[$count]['subcript'] = $res->subcript;
                $data[$count]['pin_to_top'] = $pin_to_top;
                $data[$count]['id'] = $res->id;

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

    public function updatePintotop(Request $request){
        $inputs = $request->all();
        try{
            $data_id = $inputs['data_id'] ?? '';
            $checked = (string)$inputs['checked'];
            $isChecked = ($checked == 'true') ? 1 : 0;
            if(!empty($data_id)){
                CategoryPost::where('id',$data_id)->update(['is_pin_to_top' => $isChecked]);
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
