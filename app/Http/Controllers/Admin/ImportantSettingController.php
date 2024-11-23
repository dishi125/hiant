<?php

namespace App\Http\Controllers\Admin;
use App\Models\Advance;
use Log;

use Hash;
use App\Models\User;
use ReflectionClass;
use App\Models\Config;
use App\Models\Notice;
use App\Util\Firebase;
use App\Models\Country;
use App\Models\UserDetail;
use App\Models\CreditPlans;
use App\Models\EntityTypes;
use App\Models\PackagePlan;
use App\Models\UserDevices;
use Illuminate\Support\Str;
use App\Models\PostLanguage;
use Illuminate\Http\Request;
use App\Models\ConfigLanguages;
use App\Models\GeneralSettings;
use App\Models\UserEntityRelation;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\ConfigCountryDetail;
use App\Models\LinkedSocialProfile;
use App\Models\ManagerActivityLogs;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class ImportantSettingController extends Controller
{
    protected $firebase;

    public function __construct()
    {
        $this->firebase = new Firebase();
        $this->middleware('permission:important-custom-list', ['only' => ['index','indexLimitCustom']]);
    }

    public function indexLinks()
    {
        $title = 'Links';
        return view('admin.important-setting.index-links', compact('title'));
    }

    public function getJsonLinksData(Request $request)
    {
        $draw = $request->input('draw');
        try {
            Log::info('Start important setting links');
            $user = Auth::user();
            $columns = array(
                0 => 'key',
                1 => 'value',
            );
            $limit = $request->input('length');
            $start = $request->input('start');
            $search = $request->input('search.value');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $query = Config::where('is_link',1)->select('*');

            $totalData = $query->count();
            $totalFiltered = $totalData;

            if (!empty($search)) {
                $query = $query->where(function($q) use ($search){
                    $q->where('key', 'LIKE', "%{$search}%")
                        ->orWhere('value', 'LIKE', "%{$search}%");
                });

                $totalFiltered = $query->count();
            }

            if($order == 'key' && $dir == 'asc'){
                $order = 'sort_order';
            }
            $links = $query->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $data = array();
            if (!empty($links)) {
                foreach ($links as $value) {
                    $nestedData = [];
                    $key = $value['key'] == 'Request client report sns reward email' ? 'Request Client/Report/SNS Reward Email' : $value['key'];
                    $id = $value['id'];
                    $nestedData['field'] = $key;
                    $nestedData['figure'] = $value['value'];

                    if($value['is_different_lang'] == true){
                        $edit = route('admin.important-setting.limit-custom.edit.language', $id);
                    }else{
                        $edit = route('admin.important-setting.links.edit', $id);
                    }
                    $editPostButton = "<a href='".$edit."' role='button' class='btn btn-primary btn-sm mx-1' data-toggle='tooltip' data-original-title='Edit'><i class='fa fa-edit'></a>";
                    $nestedData['actions'] = $editPostButton;

                    $data[] = $nestedData;
                }
            }
            $jsonData = array(
                "draw" => intval($draw),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data
            );
            Log::info('End important setting links');
            return response()->json($jsonData);
        } catch (\Exception $ex) {
            Log::info('Exception important setting links');
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

    public function editLinks($id)
    {
        $title = "Edit link settings";
        $settings = Config::find($id);
        $newsettings = DB::table('config')->whereId($id)->first();
        $expireMasterPassword = Config::expirePassword();
        return view('admin.important-setting.edit-links', compact('title', 'settings','newsettings'));
    }

    public function editAdvance($id)
    {
        $title = "Edit advance settings";
        $advance = Advance::find($id);

        return view('admin.important-setting.edit-advance', compact('title', 'advance'));
    }

    public function updateLinks(Request $request, $id)
    {
        try {
            Log::info('Start code for the update links setting');
            DB::beginTransaction();
            $inputs = $request->all();

            $validator = Validator::make($request->all(), [
                'key' => 'required',
                //'value' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $value = $inputs["value"];
            $key = strtolower(str_replace(' ', '_', $inputs["key"]));
            if($key == Config::ADMIN_MASTER_PASSWORD){
                $value = Hash::make($inputs['value']);
            }

            $updatePlan = Config::find($id);
            $updatePlan->value = $value ?? '';
            $updatePlan->save();

            Log::info('End the code for the update links setting');
            DB::commit();
            notify()->success("links setting updated successfully", "Success", "topRight");

            $redirect = 'admin.important-setting.links.index';
            return redirect()->route($redirect);
        } catch (\Exception $e) {
            Log::info('Exception in the update links setting.');
            Log::info($e);
            notify()->error("Failed to update links setting", "Error", "topRight");
            return redirect()->route('admin.important-setting.links.index');
        }
    }

    public function indexAdvance()
    {
        $title = 'Advance';
        return view('admin.important-setting.index-advance', compact('title'));
    }

    public function getJsonAdvanceData(Request $request)
    {
        $draw = $request->input('draw');
        try {
            Log::info('Start important setting advance');
            $user = Auth::user();
            $columns = array(
                0 => 'title',
                1 => 'is_show',
            );
            $limit = $request->input('length');
            $start = $request->input('start');
            $search = $request->input('search.value');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            $query = Advance::select('*');

            $totalData = $query->count();
            $totalFiltered = $totalData;

            if (!empty($search)) {
                $query = $query->where(function($q) use ($search){
                    $q->where('title', 'LIKE', "%{$search}%");
                });

                $totalFiltered = $query->count();
            }

            $advance = $query->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $data = array();
            if (!empty($advance)) {
                foreach ($advance as $value) {
                    $nestedData = [];
                    $id = $value['id'];
                    $nestedData['title'] = $value->title;
                    $nestedData['value'] = ($value->is_show==1) ? "Show" : "Hide";

                    $edit = route('admin.important-setting.advance.edit', $id);
                    $editButton = "<a href='".$edit."' role='button' class='btn btn-primary btn-sm mx-1' data-toggle='tooltip' data-original-title='Edit'><i class='fa fa-edit'></a>";
                    $nestedData['action'] = $editButton;

                    $data[] = $nestedData;
                }
            }
            $jsonData = array(
                "draw" => intval($draw),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data
            );
            Log::info('End important setting advance');
            return response()->json($jsonData);
        } catch (\Exception $ex) {
            Log::info('Exception important setting advance');
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

    public function updateAdvance(Request $request, $id)
    {
        try {
            Log::info('Start code for the update advance setting');
            DB::beginTransaction();
            $inputs = $request->all();

            $validator = Validator::make($request->all(), [
                'title' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $advance = Advance::find($id);
            $advance->title = $request->title ?? '';
            $advance->is_show = $request->is_show ?? '';
            $advance->save();

            Log::info('End the code for the update advance setting');
            DB::commit();
            notify()->success("advance setting updated successfully", "Success", "topRight");

            $redirect = 'admin.important-setting.advance.index';
            return redirect()->route($redirect);
        } catch (\Exception $e) {
            Log::info('Exception in the update advance setting.');
            Log::info($e);
            notify()->error("Failed to update advance setting", "Error", "topRight");
            return redirect()->route('admin.important-setting.advance.index');
        }
    }

}
