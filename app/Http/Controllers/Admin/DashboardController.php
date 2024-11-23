<?php

namespace App\Http\Controllers\Admin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $title = "Overview";
        return view('admin.dashboard.index', compact('title'));
    }

    public function updateLanguage(Request $request,$id){
        $inputs = $request->all();
        $language_id = (!empty($inputs) && $inputs['status'] == 'true') ? 1 : 2;


        User::where('id',$id)->update(['lang_id' => $language_id]);

        $jsonData = [
            'message' => "Language successfully updated.",
            'response' => true
        ];
        return response()->json($jsonData);

    }

}
