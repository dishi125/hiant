<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CMSPages;
use Log, DB;
use Illuminate\Support\Facades\Storage;

class CMSPagesController extends Controller
{
    public function index()
    {
        $title = "Policy Pages";
        return view('admin.cms-pages.index', compact('title'));
    }

    public function getJsonData(Request $request)
    {
        $columns = array(
            0 => 'title',
            1 => 'created_at',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $search = $request->input('search.value');
        $draw = $request->input('draw');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        try{
            $adminTimezone = $this->getAdminUserTimezone();
            $cmsQuery = CMSPages::select('*');

            if (!empty($search)) {
                $cmsQuery = $cmsQuery->where(function($q) use ($search){
                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('content', 'LIKE', "%{$search}%");
                });
            }

            $totalData = count($cmsQuery->get());
            $totalFiltered = $totalData;

            $cmsData = $cmsQuery->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $count = 0;
            foreach($cmsData as $page){
                $viewURL = $link = '';
                if($page->slug) {
                    $link = route('page.view', $page->slug);
                    $viewURL = "<a role='button' target='_blank' href='$link' class='btn btn-primary btn-sm mr-1'><i class='fas fa-eye'></i></a>";
                }

                $editLink = route('admin.policy-pages.edit',$page->id);
                $editButton = "<a href='$editLink' role='button' class='btn btn-primary btn-sm mx-1' data-toggle='tooltip' data-original-title='Edit'><i class='fa fa-edit' style='font-size: 15px;margin: 4px -3px 4px 0px;'></i></a>";
                $deleteButton =  "<a href='javascript:void(0);' onClick='deletePageConfirmation(".$page->id.")' class='btn-sm mx-1 btn btn-danger'><i class='fas fa-trash-alt'></i></i></a>";

                $data[$count]['name'] = $page->title;
                $data[$count]['date'] = $this->formatDateTimeCountryWise($page->created_at,$adminTimezone);

                $copyIcon = '<a href="javascript:void(0);" onClick="copyTextLink(`'.$link.'`)" class="btn-sm mx-1 btn btn-primary"><i class="fas fa-copy"></i></a>';
                $data[$count]['actions'] = "<div class='d-flex align-items-center'>$editButton $deleteButton $viewURL $copyIcon</div>";
                // $data[$count]['sub_title'] = $post->sub_title;
                $count++;
            }

            $jsonData = array(
                "draw" => intval($draw),
                "recordsTotal" => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data
            );
            return response()->json($jsonData);
        } catch (\Exception $ex) {
            Log::info($ex);
            $jsonData = array(
                "draw" => 0,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            );
            return response()->json($jsonData);
        }
    }

    public function create(){
        $title = "Create Page";
        return view('admin.cms-pages.create', compact('title'));
    }

    public function pageSlugName($pageName) {
        $pageName = str_replace(" ","-",strtolower($pageName));

        if(!CMSPages::whereSlug("$pageName")->exists()){
            return $pageName;
        }

        $i = 1;
        while(CMSPages::whereSlug("$pageName-$i")->exists()) $i++;
        return "$pageName-$i";
    }

    public function store(Request $request){
        $inputs = $request->all();
        try{
            DB::beginTransaction();

            $title = $inputs['title'] ?? '';
            $content = $inputs['content'] ?? '';

            if(!empty($title)){
                $pageSlug = $this->pageSlugName($title);
                //echo $pageSlug; exit;
                CMSPages::create([
                    'title' => $title,
                    'slug' => $pageSlug,
                    'content' => $content
                ]);
            }

            DB::commit();
            return response()->json(["success" => true, "message" => "Pages". trans("messages.insert-success"), "redirect" => route('admin.important-setting.policy-pages.index')], 200);
        } catch (\Exception $e) {
            Log::info($e);
            DB::rollback();
            return response()->json(["success" => false, "message" => "Pages". trans("messages.insert-error"), "redirect" => route('admin.important-setting.policy-pages.index')], 200);
        }
    }

    public function edit(CMSPages $page){
        $title = "Edit Page";
        return view('admin.cms-pages.create', compact('title','page'));
    }

    public function update(Request $request,$page){
        $inputs = $request->all();
        try{
            DB::beginTransaction();

            $title = $inputs['title'] ?? '';
            $content = $inputs['content'] ?? '';

            if(!empty($title)){
                $pageSlug = $this->pageSlugName($title);
                //echo $pageSlug; exit;
                CMSPages::where('id',$page)
                    ->update([
                        'title' => $title,
                        'slug' => $pageSlug,
                        'content' => $content
                    ]);
            }

            DB::commit();
            return response()->json(["success" => true, "message" => "Pages". trans("messages.update-success"), "redirect" => route('admin.important-setting.policy-pages.index')], 200);
        } catch (\Exception $e) {
            Log::info($e);
            DB::rollback();
            return response()->json(["success" => false, "message" => "Pages". trans("messages.update-error"), "redirect" => route('admin.important-setting.policy-pages.index')], 200);
        }
    }

    public function getDelete($id){
        $title = "Page";
        return view('admin.cms-pages.delete', compact('id','title'));
    }

    public function deletePage(Request $request)
    {
        $inputs = $request->all();
        try{
            $page_id = $inputs['page_id'] ?? '';
            if($page_id){
                CMSPages::where('id',$page_id)->delete();
            }
            return response()->json(["success" => true, "message" => "Page". trans("messages.delete-success")], 200);
        } catch (\Exception $e) {
            Log::info($e);
            return response()->json(["success" => false, "message" => "Page". trans("messages.delete-error")], 200);
        }
    }

    public function uploadImage(Request $request){
        if ($request->hasFile('upload')) {
            $originalName = $request->file('upload')->getClientOriginalName();

            $pagesFolder = config('constant.pages');

            if (!Storage::disk('s3')->exists($pagesFolder)) {
                Storage::disk('s3')->makeDirectory($pagesFolder);
            }

            $mainFile = Storage::disk('s3')->putFileAs($pagesFolder, $request->file('upload'), $originalName, 'public');

            return response()->json(['fileName' => $originalName, 'uploaded'=> 1, 'url' => Storage::disk('s3')->url($mainFile)]);
        }
    }

    public function viewPages($slug)
    {
        $pageData = CMSPages::where('slug',$slug)->first();
        return view("pages.view",compact('pageData'));
    }

    public function supportPage(){
        return view("pages.support");
    }
}
