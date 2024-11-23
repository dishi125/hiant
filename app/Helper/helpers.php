<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

function timeAgo($time_ago, $language = 4,$timezone = '')
{
//     dd($time_ago);
    if($timezone != '') {
        $date = new Carbon($time_ago);
        $test = $date->format('d-m-Y H:i');
        $currTime = Carbon::now()->format('Y-m-d H:i:s');
        $startTime = Carbon::createFromFormat('d-m-Y H:i',$test, "UTC")->setTimezone($timezone);
        $finishTime = Carbon::createFromFormat('Y-m-d H:i:s', $currTime, "UTC")->setTimezone($timezone);
        $time_elapsed = $finishTime->diffInSeconds($startTime);
    }else {
        $time_ago = strtotime($time_ago);
        $cur_time = time();
        $time_elapsed = $cur_time - $time_ago;
    }

    $seconds = $time_elapsed;
    $minutes = round($time_elapsed / 60);
    $hours = round($time_elapsed / 3600);
    $days = round($time_elapsed / 86400);
    $weeks = round($time_elapsed / 604800);
    $months = round($time_elapsed / 2600640);
    $years = round($time_elapsed / 31207680);

    // Seconds
    if ($seconds <= 60) {
        // return "just now";
        return __("general.just_now_$language");
    } //Minutes
    else if ($minutes <= 60) {
        if ($minutes == 1) {
            return "1". __("general.min_ago_$language");
        } else {
            return $minutes. __("general.min_ago_$language");
        }
    } //Hours
    else if ($hours <= 24) {
        if ($hours == 1) {
            return "1".__("general.hr_ago_$language");
        } else {
            return $hours.__("general.hr_ago_$language");
        }
    } //Days
    else if ($days <= 7) {
        if ($days == 1) {
            return __("general.yesterday_$language");
        } else {
            return $days.__("general.d_ago_$language");
        }
    } //Weeks
    else if ($weeks <= 4.3) {
        if ($weeks == 1) {
            return "1".__("general.w_ago_$language");
        } else {
            return $weeks.__("general.w_ago_$language");
        }
    } //Months
    else if ($months <= 12) {
        if ($months == 1) {
            return "4".__("general.w_ago_$language");
        } else {
            return ($months * 4).__("general.w_ago_$language");
        }
    } //Years
    else {
        if ($years == 1) {
            return "1".__("general.y_ago_$language");
        } else {
            return $years.__("general.y_ago_$language");
        }
    }
}

function scrape_edaily_news($keyword,$page_no){
    try {
        $url = 'https://www.edaily.co.kr/search/news/?keyword=' . $keyword . '&page=' . $page_no;
        $client = new Client();
        $response = $client->get($url);
        $htmlContent = $response->getBody()->getContents();
        $crawler = new Crawler($htmlContent);

        $data = [];
        $total_current_page = 0;
        $crawler->filter('.newsbox_04')->each(function (Crawler $result) use (&$data,&$total_current_page) {
            $headline = ($result->filter('.newsbox_texts')->filter('li')->count() > 0) ? $result->filter('.newsbox_texts')->filter('li')->first()->text() : "";
            $description = ($result->filter('.newsbox_texts')->filter('li')->eq(1)->count() > 0) ? $result->filter('.newsbox_texts')->filter('li')->eq(1)->text() : "";
            $time = "";
            if ($result->filter('.author_category')->count() > 0) {
                $authorCategory = $result->filter('.author_category');
                $time = trim($authorCategory->filterXPath('//text()')->first()->text());
            }
            $image = ($result->filter('.newsbox_visual img')->count() > 0) ? $result->filter('.newsbox_visual img')->attr('src') : "";
            $link = ($result->filter('a')->count() > 0) ? "https://www.edaily.co.kr" . $result->filter('a')->first()->attr('href') : "";
//        dd($headline,$description,$time,$image,$link);

            $view_count = \App\Models\NewsPostViewHistory::where('link', $link)->count();
            $check_blocked_post = \App\Models\ReportedNewsPost::where('type','edaily')->where('website_name','이데일리')->where('link',$link)->where('is_blocked',1)->count();
            if ($check_blocked_post == 0) {
                $data[] = [
                    'headline' => $headline,
                    'description' => $description,
                    'time' => $time,
                    'image' => "",
                    'link' => $link,
                    'view_count' => $view_count,
                    "type" => "edaily",
                    "website_name" => "이데일리"
                ];
            }
            $total_current_page++;
        });

        if ($page_no == 1) {
            $final_data = [];
            if ($crawler->filter('.searchtitle span')->count() > 0) {
                $resultCountText = $crawler->filter('.searchtitle span')->text();
                $matches = [];
                if (preg_match('/(\d+)/', $resultCountText, $matches)) {  // Extract the numeric part from the text using regular expression
                    $total_search_result = (int)$matches[1];
                }
            }
            $final_data['total_search_result'] = $total_search_result ?? 0;
            $final_data['total_current_page'] = $total_current_page;
            $final_data['data'] = $data;

            return $final_data;
        }

        return $data;
    }
    catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::info("=============Edaily website issue start=============");
        \Illuminate\Support\Facades\Log::info($e->getMessage());
        \Illuminate\Support\Facades\Log::info("=============Edaily website issue end=============");
        return false;
    }
}

function scrape_etoday_news($keyword,$page_no){
    try {
        $url = "https://www.etoday.co.kr/search/?keyword=$keyword&fldTermStart=&fldTermEnd=&fldTermType=1&fldSort=1&page=$page_no";
        $client = new Client();
        $response = $client->get($url);
        $htmlContent = $response->getBody()->getContents();
        $crawler = new Crawler($htmlContent);

        $data = [];
        $total_current_page = 0;
        $crawler->filter('.sp_newslist')->each(function (Crawler $result) use (&$data,&$total_current_page) {
            $headline = ($result->filter('.cluster_text_headline21 a')->count() > 0) ? $result->filter('.cluster_text_headline21 a')->text() : "";
            $description = ($result->filter('.cluster_text_lede21')->count() > 0) ? $result->filter('.cluster_text_lede21')->text() : "";
            $time = ($result->filter('.cluster_text_press21')->count() > 0) ? $result->filter('.cluster_text_press21')->text() : "";
            $image = ($result->filter('.cluster_thumb_link21')->filter('img')->count() > 0) ? $result->filter('.cluster_thumb_link21')->filter('img')->attr('src') : "";
            $link = ($result->filter('.cluster_text_headline21 a')->count() > 0) ? "https://www.etoday.co.kr" . $result->filter('.cluster_text_headline21 a')->attr('href') : "";

            $view_count = \App\Models\NewsPostViewHistory::where('link', $link)->count();
            $check_blocked_post = \App\Models\ReportedNewsPost::where('type','etoday')->where('website_name','이투데이')->where('link',$link)->where('is_blocked',1)->count();
            if ($check_blocked_post==0) {
                $data[] = [
                    'headline' => $headline,
                    'description' => $description,
                    'time' => $time,
                    'image' => $image,
                    'link' => $link,
                    'view_count' => $view_count,
                    "type" => "etoday",
                    "website_name" => "이투데이"
                ];
            }
            $total_current_page++;
        });

        if ($page_no == 1) {
            $final_data = [];
            if ($crawler->filter('.search_lst strong')->count() > 0) {
                $total_search_result = (int)$crawler->filter('.search_lst strong')->text();
            }
            $final_data['total_search_result'] = $total_search_result ?? 0;
            $final_data['total_current_page'] = $total_current_page;
            $final_data['data'] = $data;

            return $final_data;
        }

        return $data;
    }
    catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::info("=============Etoday website issue start=============");
        \Illuminate\Support\Facades\Log::info($e->getMessage());
        \Illuminate\Support\Facades\Log::info("=============Etoday website issue end=============");
        return false;
    }
}

function scrape_fnnews_news($keyword,$page_no){
    try {
        $url = "https://www.fnnews.com/search?page=$page_no&search_type=&cont_type=&period_type=&searchDateS=&searchDateE=&search_txt=" . $keyword;
        $client = new Client();
        $response = $client->get($url);
        $htmlContent = $response->getBody()->getContents();
        $crawler = new Crawler($htmlContent);

        $data = [];
        $total_current_page = 0;
        $crawler->filter('.list_art li')->each(function (Crawler $result) use (&$data,&$total_current_page) {
            $headline = ($result->filter('.tit_thumb')->count() > 0) ? $result->filter('.tit_thumb')->text() : "";
            $description = ($result->filter('.txt_dec')->count() > 0) ? $result->filter('.txt_dec')->text() : "";
            $time = ($result->filter('.date')->count() > 0) ? $result->filter('.date')->text() : "";
            $image = ($result->filter('.thumb_img img')->count() > 0) ? $result->filter('.thumb_img img')->attr('src') : "";
            $link = ($result->filter('a')->count() > 0) ? "https://www.fnnews.com" . $result->filter('a')->first()->attr('href') : "";
//        dd($headline,$description,$time,$image,$link);

            $view_count = \App\Models\NewsPostViewHistory::where('link', $link)->count();
            $check_blocked_post = \App\Models\ReportedNewsPost::where('type','fnnews')->where('website_name','파이낸셜뉴스')->where('link',$link)->where('is_blocked',1)->count();
            if ($check_blocked_post==0) {
                $data[] = [
                    'headline' => $headline,
                    'description' => $description,
                    'time' => $time,
                    'image' => $image,
                    'link' => $link,
                    'view_count' => $view_count,
                    "type" => "fnnews",
                    "website_name" => "파이낸셜뉴스"
                ];
            }
            $total_current_page++;
        });

        if ($page_no == 1) {
            $final_data = [];
            if ($crawler->filter('.ouput_search strong')->eq(1)->count() > 0) {
                $total_search_result = (int)$crawler->filter('.ouput_search strong')->eq(1)->text();
            }
            $final_data['total_search_result'] = $total_search_result ?? 0;
            $final_data['total_current_page'] = $total_current_page;
            $final_data['data'] = $data;

            return $final_data;
        }

        return $data;
    }
    catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::info("=============Fnnews website issue start=============");
        \Illuminate\Support\Facades\Log::info($e->getMessage());
        \Illuminate\Support\Facades\Log::info("=============Fnnews website issue end=============");
        return false;
    }
}


function scrape_hankyung_news($keyword,$page_no){
    try {
//    $url = "https://search.hankyung.com/search/news?query=".$keyword;
        $url = "https://search.hankyung.com/search/news?query=$keyword&page=$page_no";
        $client = new Client();
        $response = $client->get($url);
        $htmlContent = $response->getBody()->getContents();
        $crawler = new Crawler($htmlContent);

        $data = [];
        $total_current_page = 0;
        $crawler->filter('.section_cont li')->each(function (Crawler $result) use (&$data,&$total_current_page) {
            $headline = ($result->filter('a')->eq(1)->filter('em.tit')->count() > 0) ? $result->filter('a')->eq(1)->filter('em.tit')->text() : "";
            $description = ($result->filter('.txt')->count() > 0) ? $result->filter('.txt')->text() : "";
            $time = ($result->filter('.date_time')->count() > 0) ? $result->filter('.date_time')->text() : "";
            $image = ($result->filter('.thumbnail img')->count() > 0) ? $result->filter('.thumbnail img')->attr('src') : "";
            if ($image != "" && !\Illuminate\Support\Str::startsWith($image, "https://")) {
                $image = \Illuminate\Support\Str::replaceFirst("//", "https://", $image);
            }
            $link = ($result->filter('a')->count() > 0) ? $result->filter('a')->first()->attr('href') : "";
//        dd($headline,$description,$time,$image,$link);

            $view_count = \App\Models\NewsPostViewHistory::where('link', $link)->count();
            $check_blocked_post = \App\Models\ReportedNewsPost::where('type','hankyung')->where('website_name','한국경제')->where('link',$link)->where('is_blocked',1)->count();
            if ($check_blocked_post==0) {
                $data[] = [
                    'headline' => $headline,
                    'description' => $description,
                    'time' => $time,
                    'image' => $image,
                    'link' => $link,
                    'view_count' => $view_count,
                    "type" => "hankyung",
                    "website_name" => "한국경제"
                ];
            }
            $total_current_page++;
        });

        if ($page_no == 1) {
            $final_data = [];
            if ($crawler->filter('.hk_news .tit')->first()->filter('span')->count() > 0) {
                $resultCountText = $crawler->filter('.hk_news .tit')->first()->filter('span')->text();
                $matches = [];
                if (preg_match('/(\d+)건/', $resultCountText, $matches)) {
                    $total_search_result = (int)$matches[1];
                }
            }

            $final_data['total_search_result'] = $total_search_result ?? 0;
            $final_data['total_current_page'] = $total_current_page;
            $final_data['data'] = $data;

            return $final_data;
        }

        return $data;
    }
    catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::info("=============Hankyung website issue start=============");
        \Illuminate\Support\Facades\Log::info($e->getMessage());
        \Illuminate\Support\Facades\Log::info("=============Hankyung website issue end=============");
        return false;
    }
}

function scrape_heraldcorp_news($keyword,$page_no){
    try {
        $url = "http://biz.heraldcorp.com/search/index.php?q=$keyword&np=$page_no";
        $client = new Client();
        $response = $client->get($url);
        $htmlContent = $response->getBody()->getContents();
        $crawler = new Crawler($htmlContent);

        $data = [];
        $total_current_page = 0;
        $crawler->filter('.list li')->each(function (Crawler $result) use (&$data,&$total_current_page) {
            $headline = ($result->filter('.list_title')->count() > 0) ? $result->filter('.list_title')->text() : "";
            $description = ($result->filter('.list_txt')->count() > 0) ? $result->filter('.list_txt')->text() : "";
            $time = ($result->filter('.l_date')->count() > 0) ? $result->filter('.l_date')->text() : "";
            $image = ($result->filter('.list_img img')->count() > 0) ? $result->filter('.list_img img')->attr('src') : "";
            if ($image != "" && !\Illuminate\Support\Str::startsWith($image, "https://")) {
                $image = \Illuminate\Support\Str::replaceFirst("//", "https://", $image);
            }
            $link = ($result->filter('a')->count() > 0) ? "http://biz.heraldcorp.com" . $result->filter('a')->first()->attr('href') : "";
//        dd($headline,$description,$time,$image,$link);

            $view_count = \App\Models\NewsPostViewHistory::where('link', $link)->count();
            $check_blocked_post = \App\Models\ReportedNewsPost::where('type','heraldcorp')->where('website_name','헤럴드경제')->where('link',$link)->where('is_blocked',1)->count();
            if ($check_blocked_post==0) {
                $data[] = [
                    'headline' => $headline,
                    'description' => $description,
                    'time' => $time,
                    'image' => $image,
                    'link' => $link,
                    'view_count' => $view_count,
                    "type" => "heraldcorp",
                    "website_name" => "헤럴드경제"
                ];
            }
            $total_current_page++;
        });

        if ($page_no == 1) {
            $final_data = [];
            if ($crawler->filter('.result_area strong')->eq(1)->count() > 0) {
                $total_search_result = (int)$crawler->filter('.result_area strong')->eq(1)->text();
            }

            $final_data['total_search_result'] = $total_search_result ?? 0;
            $final_data['total_current_page'] = $total_current_page;
            $final_data['data'] = $data;

            return $final_data;
        }

        return $data;
    }
    catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::info("=============Heraldcorp website issue start=============");
        \Illuminate\Support\Facades\Log::info($e->getMessage());
        \Illuminate\Support\Facades\Log::info("=============Heraldcorp website issue end=============");
        return false;
    }
}

function scrape_joseilbo_news($keyword,$page_no){
    try {
        $url = "http://www.joseilbo.com/fastcat/search_new.php?search_action_check=true&currentPage=$page_no&searchField=all&search_collection=news&search_keyword=$keyword&currentPage2=1&currentPage4=1&sdate=&edate=&interval=all";
        $client = new Client();
        $response = $client->get($url);
        $htmlContent = $response->getBody()->getContents();
        $crawler = new Crawler($htmlContent);

        $data = [];
        $total_current_page = 0;
        $crawler->filter('#news_search_list .article')->each(function (Crawler $result) use (&$data,&$total_current_page) {
            $headline = ($result->filter('.tit a')->count() > 0) ? $result->filter('.tit a')->text() : "";
            $description = "";
            if ($result->filter('.txt')->count() > 0) {
                $desc = $result->filter('.txt');
                $description = trim($desc->filterXPath('//text()')->first()->text());
            }
            $time = ($result->filter('.txt span')->count() > 0) ? $result->filter('.txt span')->first()->text() : "";
            $image = ($result->filter('.article_pic_box img')->count() > 0) ? "http://www.joseilbo.com" . ltrim($result->filter('.article_pic_box img')->attr('src'), ".") : "";
            $link = ($result->filter('.tit a')->count() > 0) ? "http://www.joseilbo.com" . $result->filter('.tit a')->first()->attr('href') : "";
//        dd($headline,$description,$time,$image,$link);

            $view_count = \App\Models\NewsPostViewHistory::where('link', $link)->count();
            $check_blocked_post = \App\Models\ReportedNewsPost::where('type','joseilbo')->where('website_name','조세일보')->where('link',$link)->where('is_blocked',1)->count();
            if ($check_blocked_post==0) {
                $data[] = [
                    'headline' => $headline,
                    'description' => $description,
                    'time' => $time,
                    'image' => $image,
                    'link' => $link,
                    'view_count' => $view_count,
                    "type" => "joseilbo",
                    "website_name" => "조세일보"
                ];
            }
            $total_current_page++;
        });

        if ($page_no == 1) {
            $final_data = [];
            if ($crawler->filter('.search_article_list .total')->count() > 0) {
                $totalText = $crawler->filter('.search_article_list .total')->first()->text();
                $matches = [];
                if (preg_match('/(\d+)/', $totalText, $matches)) {
                    $total_search_result = (int)$matches[1];
                }
            }

            $final_data['total_search_result'] = $total_search_result ?? 0;
            $final_data['total_current_page'] = $total_current_page;
            $final_data['data'] = $data;

            return $final_data;
        }

        return $data;
    }
    catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::info("=============Joseilbo website issue start=============");
        \Illuminate\Support\Facades\Log::info($e->getMessage());
        \Illuminate\Support\Facades\Log::info("=============Joseilbo website issue end=============");
        return false;
    }
}

function scrape_mk_news($keyword){
    try {
        $url = "https://www.mk.co.kr/search/news?word=" . $keyword;
        $client = new Client();
        $response = $client->get($url);
        $htmlContent = $response->getBody()->getContents();
        $crawler = new Crawler($htmlContent);

        $data = [];
        $crawler->filter('.news_list .news_node')->each(function (Crawler $result) use (&$data) {
            $headline = ($result->filter('.news_ttl')->count() > 0) ? $result->filter('.news_ttl')->text() : "";
            $description = ($result->filter('.news_desc')->count() > 0) ? $result->filter('.news_desc')->text() : "";
            $time = ($result->filter('.time_info')->count() > 0) ? $result->filter('.time_info')->text() : "";
            $image = ($result->filter('.thumb_area img')->count() > 0) ? $result->filter('.thumb_area img')->attr('data-src') : "";
            $link = ($result->filter('.news_item')->count() > 0) ? $result->filter('.news_item')->attr('href') : "";
//        dd($headline,$description,$time,$image,$link);

            $view_count = \App\Models\NewsPostViewHistory::where('link', $link)->count();
            $check_blocked_post = \App\Models\ReportedNewsPost::where('type','mk')->where('website_name','매일경제')->where('link',$link)->where('is_blocked',1)->count();
            if ($check_blocked_post==0) {
                $data[] = [
                    'headline' => $headline,
                    'description' => $description,
                    'time' => $time,
                    'image' => $image,
                    'link' => $link,
                    'view_count' => $view_count,
                    "type" => "mk",
                    "website_name" => "매일경제"
                ];
            }
        });

        return $data;
    }
    catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::info("=============Mk website issue start=============");
        \Illuminate\Support\Facades\Log::info($e->getMessage());
        \Illuminate\Support\Facades\Log::info("=============Mk website issue end=============");
        return false;
    }
}

function scrape_mt_news($keyword,$page_no){
    try {
        $url = "https://search.mt.co.kr/searchNewsList.html?srchFd=TOTAL&range=TOTAL&reSrchFlag=&preKwd=&search_type=m&kwd=$keyword&bgndt=&enddt=&category=MTNW&sortType=allwordsyn&subYear=&category=MTNW&subType=mt&pageNum=$page_no";
        $client = new Client();
        $response = $client->get($url);
        $htmlContent = $response->getBody()->getContents();
        $crawler = new Crawler($htmlContent);

        $data = [];
        $total_current_page = 0;
        $crawler->filter('.conlist_p1 li')->each(function (Crawler $result) use (&$data,&$total_current_page) {
            $headline = ($result->filter('.subject a')->count() > 0) ? $result->filter('.subject a')->text() : "";
            $description = ($result->filter('.txt a')->count() > 0) ? $result->filter('.txt a')->text() : "";
            $time = "";
            if ($result->filter('.etc')->count() > 0) {
                $etcText = $result->filter('.etc')->text();
                $pattern = '/\d{4}\.\d{2}\.\d{2} \d{2}:\d{2}/';
                preg_match($pattern, $etcText, $matches);
                $time = $matches[0] ?? '';
            }
            $image = ($result->filter('img')->count() > 0) ? $result->filter('img')->first()->attr('src') : "";
            $link = ($result->filter('.subject a')->count() > 0) ? $result->filter('.subject a')->attr('href') : "";
//        dd($headline,$description,$time,$image,$link);

            $view_count = \App\Models\NewsPostViewHistory::where('link', $link)->count();
            $check_blocked_post = \App\Models\ReportedNewsPost::where('type','mt')->where('website_name','머니투데이')->where('link',$link)->where('is_blocked',1)->count();
            if ($check_blocked_post==0) {
                $data[] = [
                    'headline' => $headline,
                    'description' => $description,
                    'time' => $time,
                    'image' => $image,
                    'link' => $link,
                    'view_count' => $view_count,
                    "type" => "mt",
                    "website_name" => "머니투데이"
                ];
            }
            $total_current_page++;
        });

        if ($page_no == 1) {
            $final_data = [];
            if ($crawler->filter('.search_area a')->count() > 0) {
                $resultCountText = $crawler->filter('.search_area a')->first()->text();
                $matches = [];
                if (preg_match('/\((\d+)\s*건\)/', $resultCountText, $matches)) {
                    $total_search_result = (int)$matches[1];
                }
            }

            $final_data['total_search_result'] = $total_search_result ?? 0;
            $final_data['total_current_page'] = $total_current_page;
            $final_data['data'] = $data;

            return $final_data;
        }

        return $data;
    }
    catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::info("=============Mt website issue start=============");
        \Illuminate\Support\Facades\Log::info($e->getMessage());
        \Illuminate\Support\Facades\Log::info("=============Mt website issue end=============");
        return false;
    }
}

function scrape_newspim_news($keyword,$page_no){
    try {
        $url = "https://www.newspim.com/search?type=news&searchword=$keyword&searchfor%5B0%5D=1&sort=desc&period=all&searchrange%5B0%5D=title&searchrange%5B1%5D=contents&searchrange%5B2%5D=reporter&page=$page_no";
        $client = new Client();
        $response = $client->get($url);
        $htmlContent = $response->getBody()->getContents();
        $crawler = new Crawler($htmlContent);

        $data = [];
        $total_current_page = 0;
        $crawler->filter('.news article')->each(function (Crawler $result) use (&$data,&$total_current_page) {
            $headline = ($result->filter('.subject a')->count() > 0) ? $result->filter('.subject a')->text() : "";
            $description = ($result->filter('.summary')->count() > 0) ? $result->filter('.summary')->text() : "";
            $time = ($result->filter('.date')->count() > 0) ? $result->filter('.date')->text() : "";
            $image = ($result->filter('.thumb img')->count() > 0) ? $result->filter('.thumb img')->attr('src') : "";
            $link = ($result->filter('.subject a')->count() > 0) ? "https://www.newspim.com" . $result->filter('.subject a')->attr('href') : "";
//        dd($headline,$description,$time,$image,$link);

            $view_count = \App\Models\NewsPostViewHistory::where('link', $link)->count();
            $check_blocked_post = \App\Models\ReportedNewsPost::where('type','newspim')->where('website_name','뉴스핌')->where('link',$link)->where('is_blocked',1)->count();
            if ($check_blocked_post==0) {
                $data[] = [
                    'headline' => $headline,
                    'description' => $description,
                    'time' => $time,
                    'image' => $image,
                    'link' => $link,
                    'view_count' => $view_count,
                    "type" => "newspim",
                    "website_name" => "뉴스핌"
                ];
            }
            $total_current_page++;
        });

        if ($page_no == 1) {
            $final_data = [];
            if ($crawler->filter('.result span:nth-child(2)')->count() > 0) {
                $resultCountText = $crawler->filter('.result span:nth-child(2)')->text();
                $matches = [];
                if (preg_match('/(\d+)/', $resultCountText, $matches)) {
                    $total_search_result = (int)$matches[1];
                }
            }

            $final_data['total_search_result'] = $total_search_result ?? 0;
            $final_data['total_current_page'] = $total_current_page;
            $final_data['data'] = $data;

            return $final_data;
        }

        return $data;
    }
    catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::info("=============Newspim website issue start=============");
        \Illuminate\Support\Facades\Log::info($e->getMessage());
        \Illuminate\Support\Facades\Log::info("=============Newspim website issue end=============");
        return false;
    }
}
