<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advance;
use App\Models\Category;
use App\Models\CategoryPost;
use App\Models\NewsPostViewHistory;
use App\Models\NewsWebsite;
use App\Models\ReportedNewsPost;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index(Request $request){
        $user = Auth::user();
        try {
            if($user){
                DB::beginTransaction();

//                $top_posts = CategoryPost::where('is_pin_to_top',1)->get(['id','category_id','title','agency_name','image','subcript']);
                $today = Carbon::now()->format('Y-m-d');
                $top_posts = NewsPostViewHistory::select('link','headline','description','time','image',DB::raw('COUNT(*) as today_view_count'),'website_name','type')
                    ->whereDate('created_at', $today)
                    ->groupBy('link')
                    ->orderByDesc('today_view_count')
                    ->orderBy('created_at') // To get the earliest occurrence if counts are the same
                    ->take(5)
                    ->get();

                $categories = Category::orderBy('order','ASC')->get(['id','category']);

                $initial_cat_posts = [];
                if (!empty($categories)) {
//                    $initial_cat_posts = CategoryPost::where('category_id', $categories[0]['id'])->get(['id', 'category_id', 'title', 'agency_name', 'image', 'subcript']);
                    $news_web_orders = NewsWebsite::orderBy('order','ASC')->get();
                    foreach ($news_web_orders as $news_web){
                        if ($news_web->type=="edaily"){
                            $edaily = scrape_edaily_news($categories[0]['category'],1);
                            if ($edaily!=false && isset($edaily['data']) && !empty($edaily['data'])) {
                                $initial_cat_posts[] = array("type" => "edaily",
                                    "website_name" => "이데일리",
                                    "total_search_result" => $edaily['total_search_result'],
                                    "total_current_page" => $edaily['total_current_page'],
                                    "data" => $edaily['data']); //image url is not valid
                            }
                        }
                        else if ($news_web->type=="etoday"){
                            $etoday = scrape_etoday_news($categories[0]['category'],1);
                            if ($etoday!=false && isset($etoday['data']) && !empty($etoday['data'])) {
                                $initial_cat_posts[] = array("type" => "etoday",
                                    "website_name" => "이투데이",
                                    "total_search_result" => $etoday['total_search_result'],
                                    "total_current_page" => $etoday['total_current_page'],
                                    "data" => $etoday['data']);
                            }
                        }
                        else if ($news_web->type=="fnnews"){
                            $fnnews = scrape_fnnews_news($categories[0]['category'],1);
                            if ($fnnews!=false && isset($fnnews['data']) && !empty($fnnews['data'])) {
                                $initial_cat_posts[] = array("type" => "fnnews",
                                    "website_name" => "파이낸셜뉴스",
                                    "total_search_result" => $fnnews['total_search_result'],
                                    "total_current_page" => $fnnews['total_current_page'],
                                    "data" => $fnnews['data']);
                            }
                        }
                        else if ($news_web->type=="hankyung"){
                            $hankyung = scrape_hankyung_news($categories[0]['category'],1);
                            if ($hankyung!=false && isset($hankyung['data']) && !empty($hankyung['data'])) {
                                $initial_cat_posts[] = array("type" => "hankyung",
                                    "website_name" => "한국경제",
                                    "total_search_result" => $hankyung['total_search_result'],
                                    "total_current_page" => $hankyung['total_current_page'],
                                    "data" => $hankyung['data']);
                            }
                        }
                        else if ($news_web->type=="heraldcorp"){
                            $heraldcorp = scrape_heraldcorp_news($categories[0]['category'],1);
                            if ($heraldcorp!=false && isset($heraldcorp['data']) && !empty($heraldcorp['data'])) {
                                $initial_cat_posts[] = array("type" => "heraldcorp",
                                    "website_name" => "헤럴드경제",
                                    "total_search_result" => $heraldcorp['total_search_result'],
                                    "total_current_page" => $heraldcorp['total_current_page'],
                                    "data" => $heraldcorp['data']);
                            }
                        }
                        else if ($news_web->type=="joseilbo"){
                            $joseilbo = scrape_joseilbo_news($categories[0]['category'],1);
                            if ($joseilbo!=false && isset($joseilbo['data']) && !empty($joseilbo['data'])) {
                                $initial_cat_posts[] = array("type" => "joseilbo",
                                    "website_name" => "조세일보",
                                    "total_search_result" => $joseilbo['total_search_result'],
                                    "total_current_page" => $joseilbo['total_current_page'],
                                    "data" => $joseilbo['data']);
                            }
                        }
                        else if ($news_web->type=="mk"){
                            $mk_data = scrape_mk_news($categories[0]['category']);
                            if ($mk_data!=false && !empty($mk_data)) {
                                $initial_cat_posts[] = array("type" => "mk",
                                    "website_name" => "매일경제",
                                    "total_search_result" => 0,
                                    "total_current_page" => 0,
                                    "data" => $mk_data);
                            }
                        }
                        else if ($news_web->type=="mt"){
                            $mt = scrape_mt_news($categories[0]['category'],1);
                            if ($mt!=false && isset($mt['data']) && !empty($mt['data'])) {
                                $initial_cat_posts[] = array("type" => "mt",
                                    "website_name" => "머니투데이",
                                    "total_search_result" => $mt['total_search_result'],
                                    "total_current_page" => $mt['total_current_page'],
                                    "data" => $mt['data']);
                            }
                        }
                        else if ($news_web->type=="newspim"){
                            $newspim = scrape_newspim_news($categories[0]['category'],1);
                            if ($newspim!=false && isset($newspim['data']) && !empty($newspim['data'])) {
                                $initial_cat_posts[] = array("type" => "newspim",
                                    "website_name" => "뉴스핌",
                                    "total_search_result" => $newspim['total_search_result'],
                                    "total_current_page" => $newspim['total_current_page'],
                                    "data" => $newspim['data']);
                            }
                        }
                    }
                }

                $data['top_posts'] = $top_posts;
                $data['categories'] = $categories;
                $data['initial_category_posts'] = $initial_cat_posts;
                DB::commit();
                return $this->sendSuccessResponse("Home page data.", 200, $data);
            }else{
                return $this->sendSuccessResponse(Lang::get('messages.user.token_expired'), 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info("=========Home API issue start==========");
            Log::info($e->getMessage());
            Log::info("=========Home API issue end============");
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

    public function searchNews(Request $request){
        $user = Auth::user();
        $inputs = $request->all();
        try {
            if($user){
                DB::beginTransaction();

                $news_web_orders = NewsWebsite::orderBy('order','ASC')->get();
                foreach ($news_web_orders as $news_web){
                    if ($news_web->type=="edaily"){
                        $edaily = scrape_edaily_news($inputs['keyword'],1);
                        if ($edaily!=false && isset($edaily['data']) && !empty($edaily['data'])) {
                            $data[] = array("type" => "edaily",
                                "website_name" => "이데일리",
                                "total_search_result" => $edaily['total_search_result'],
                                "total_current_page" => $edaily['total_current_page'],
                                "data" => $edaily['data']); //image url is not valid
                        }
                    }
                    elseif ($news_web->type=="etoday"){
                        $etoday = scrape_etoday_news($inputs['keyword'], 1);
                        if ($etoday!=false && isset($etoday['data']) &&!empty($etoday['data'])) {
                            $data[] = array("type" => "etoday",
                                "website_name" => "이투데이",
                                "total_search_result" => $etoday['total_search_result'],
                                "total_current_page" => $etoday['total_current_page'],
                                "data" => $etoday['data']);
                        }
                    }
                    elseif ($news_web->type=="fnnews"){
                        $fnnews = scrape_fnnews_news($inputs['keyword'], 1);
                        if ($fnnews!=false && isset($fnnews['data']) && !empty($fnnews['data'])) {
                            $data[] = array("type" => "fnnews",
                                "website_name" => "파이낸셜뉴스",
                                "total_search_result" => $fnnews['total_search_result'],
                                "total_current_page" => $fnnews['total_current_page'],
                                "data" => $fnnews['data']);
                        }
                    }
                    elseif ($news_web->type=="hankyung"){
                        $hankyung = scrape_hankyung_news($inputs['keyword'], 1);
                        if ($hankyung!=false && isset($hankyung['data']) && !empty($hankyung['data'])) {
                            $data[] = array("type" => "hankyung",
                                "website_name" => "한국경제",
                                "total_search_result" => $hankyung['total_search_result'],
                                "total_current_page" => $hankyung['total_current_page'],
                                "data" => $hankyung['data']);
                        }
                    }
                    elseif ($news_web->type=="heraldcorp"){
                        $heraldcorp = scrape_heraldcorp_news($inputs['keyword'], 1);
                        if ($heraldcorp!=false && isset($heraldcorp['data']) && !empty($heraldcorp['data'])) {
                            $data[] = array("type" => "heraldcorp",
                                "website_name" => "헤럴드경제",
                                "total_search_result" => $heraldcorp['total_search_result'],
                                "total_current_page" => $heraldcorp['total_current_page'],
                                "data" => $heraldcorp['data']);
                        }
                    }
                    elseif ($news_web->type=="joseilbo"){
                        $joseilbo = scrape_joseilbo_news($inputs['keyword'], 1);
                        if ($joseilbo!=false && isset($joseilbo['data']) && !empty($joseilbo['data'])) {
                            $data[] = array("type" => "joseilbo",
                                "website_name" => "조세일보",
                                "total_search_result" => $joseilbo['total_search_result'],
                                "total_current_page" => $joseilbo['total_current_page'],
                                "data" => $joseilbo['data']);
                        }
                    }
                    elseif ($news_web->type=="mk"){
                        $mk_data = scrape_mk_news($inputs['keyword']);
                        if ($mk_data!=false && !empty($mk_data)) {
                            $data[] = array("type" => "mk",
                                "website_name" => "매일경제",
                                "total_search_result" => 0,
                                "total_current_page" => 0,
                                "data" => $mk_data);
                        }
                    }
                    elseif ($news_web->type=="mt"){
                        $mt = scrape_mt_news($inputs['keyword'],1);
                        if ($mt!=false && isset($mt['data']) && !empty($mt['data'])) {
                            $data[] = array("type" => "mt",
                                "website_name" => "머니투데이",
                                "total_search_result" => $mt['total_search_result'],
                                "total_current_page" => $mt['total_current_page'],
                                "data" => $mt['data']);
                        }
                    }
                    elseif ($news_web->type=="newspim"){
                        $newspim = scrape_newspim_news($inputs['keyword'],1);
                        if ($newspim!=false && isset($newspim['data']) && !empty($newspim['data'])) {
                            $data[] = array("type" => "newspim",
                                "website_name" => "뉴스핌",
                                "total_search_result" => $newspim['total_search_result'],
                                "total_current_page" => $newspim['total_current_page'],
                                "data" => $newspim['data']);
                        }
                    }
                }

                DB::commit();
                return $this->sendSuccessResponse("News result data.", 200, $data);
            }else{
                return $this->sendSuccessResponse(Lang::get('messages.user.token_expired'), 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info("=========search-news API issue start==========");
            Log::info($e->getMessage());
            Log::info("=========search-news API issue end============");
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

    public function searchMoreNews(Request $request){
        $user = Auth::user();
        $inputs = $request->all();
        try {
            if($user){
                DB::beginTransaction();

                if ($inputs['type'] == "edaily"){
                    $scrapped_data = scrape_edaily_news($inputs['keyword'],$inputs['page']); //image url is not valid
                    $data = $scrapped_data!=false ? $scrapped_data : [];
                }
                elseif ($inputs['type'] == "etoday"){
                    $scrapped_data = scrape_etoday_news($inputs['keyword'],$inputs['page']);
                    $data = $scrapped_data!=false ? $scrapped_data : [];
                }
                elseif ($inputs['type'] == "fnnews"){
                    $scrapped_data = scrape_fnnews_news($inputs['keyword'],$inputs['page']);
                    $data = $scrapped_data!=false ? $scrapped_data : [];
                }
                elseif ($inputs['type'] == "hankyung"){
                    $scrapped_data = scrape_hankyung_news($inputs['keyword'],$inputs['page']);
                    $data = $scrapped_data!=false ? $scrapped_data : [];
                }
                elseif ($inputs['type'] == "heraldcorp"){
                    $scrapped_data = scrape_heraldcorp_news($inputs['keyword'],$inputs['page']);
                    $data = $scrapped_data!=false ? $scrapped_data : [];
                }
                elseif ($inputs['type'] == "joseilbo"){
                    $scrapped_data = scrape_joseilbo_news($inputs['keyword'],$inputs['page']);
                    $data = $scrapped_data!=false ? $scrapped_data : [];
                }
                elseif ($inputs['type'] == "mt"){
                    $scrapped_data = scrape_mt_news($inputs['keyword'],$inputs['page']);
                    $data = $scrapped_data!=false ? $scrapped_data : [];
                }
                elseif ($inputs['type'] == "newspim"){
                    $scrapped_data = scrape_newspim_news($inputs['keyword'],$inputs['page']);
                    $data = $scrapped_data!=false ? $scrapped_data : [];
                }

                DB::commit();
                return $this->sendSuccessResponse("News result data.", 200, $data);
            }else{
                return $this->sendSuccessResponse(Lang::get('messages.user.token_expired'), 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info("=========search-more-news API issue start==========");
            Log::info($e->getMessage());
            Log::info("=========search-more-news API issue end============");
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

    public function viewNewsPost(Request $request){
        $user = Auth::user();
        $inputs = $request->all();
        try {
            if($user){
                DB::beginTransaction();

                if ($inputs['link']!="") {
                    NewsPostViewHistory::firstOrCreate([
                        'link' => $inputs['link'],
                        'user_id' => $user->id,
                    ],[
                        'type' => $inputs['type'],
                        'website_name' => $inputs['website_name'],
                        'headline' => $inputs['headline'],
                        'description' => $inputs['description'],
                        'time' => $inputs['time'],
                        'image' => $inputs['image'],
                    ]);
                }
                $view_count = \App\Models\NewsPostViewHistory::where('link',$inputs['link'])->count();

                DB::commit();
                return $this->sendSuccessResponse("View count data.", 200, $view_count);
            }else{
                return $this->sendSuccessResponse(Lang::get('messages.user.token_expired'), 401);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info("=========view-news-post API issue start==========");
            Log::info($e->getMessage());
            Log::info("=========view-news-post API issue end============");
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

    public function reportNewsPost(Request $request){
        $user = Auth::user();
        $inputs = $request->all();
        try {
            DB::beginTransaction();
            $userID = $user->id;

            if(!empty($userID)){
                if(!isset($inputs['reason'])){
                    return $this->sendFailedResponse("Please enter reason for remove account.", 400);
                }

                if(strlen(trim($inputs['reason'])) < 10){
                    return $this->sendFailedResponse("Please enter atleast 10 words in reason.", 400);
                }

                ReportedNewsPost::create([
                    'type' => $inputs['type'],
                    'website_name' => $inputs['website_name'],
                    'link' => $inputs['link'],
                    'user_id' => $user->id,
                    'reason' => $inputs['reason'],
                ]);
            }

            DB::commit();
            return $this->sendSuccessResponse("News post report request has been successfully completed.", 200, []);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info("=========report-news-post API issue start==========");
            Log::info($e->getMessage());
            Log::info("=========report-news-post API issue end============");
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

    public function advanceSettings(Request $request){
        $user = Auth::user();
        $inputs = $request->all();
        try {
            DB::beginTransaction();

            $settings = Advance::get(['key','is_show']);

            DB::commit();
            return $this->sendSuccessResponse("Advance settings.", 200, $settings);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info("=========advance-settings API issue start==========");
            Log::info($e->getMessage());
            Log::info("=========advance-settings API issue end============");
            return $this->sendFailedResponse(Lang::get('messages.general.laravel_error'), 400);
        }
    }

}
