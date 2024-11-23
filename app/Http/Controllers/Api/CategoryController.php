<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

class CategoryController extends Controller
{
    public function list(){
        $user = Auth::user();
        try {
            if($user){
                DB::beginTransaction();

                $categories = Category::orderBy('order','ASC')->get(['id','category']);

                DB::commit();
                return $this->sendSuccessResponse("Category list.", 200, $categories);
            }else{
                return $this->sendSuccessResponse(Lang::get('messages.user.token_expired'), 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

    public function postList(Request $request){
        $user = Auth::user();
        try {
            if($user){
                DB::beginTransaction();
                $inputs = $request->all();

                $posts = CategoryPost::where('category_id',$inputs['category_id'])->get(['id','category_id','title','agency_name','image','subcript']);

                DB::commit();
                return $this->sendSuccessResponse("Post list.", 200, $posts);
            }else{
                return $this->sendSuccessResponse(Lang::get('messages.user.token_expired'), 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

}
