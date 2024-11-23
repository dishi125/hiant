<?php
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */
$exceptMethods = ['except' => ['create', 'edit']];

Route::name('api')->namespace('Api')->group(function () {
    // Authentication
    Route::post('/register', 'AuthenticateController@register')->name('.auth.register');
    Route::post('/register-validate', 'AuthenticateController@registerValidate')->name('.validate.register');
    Route::post('/login', 'AuthenticateController@login')->name('.auth.login');
    Route::post('/forgot/email', 'AuthenticateController@forgotEmail')->name('.auth.forgot.email'); // for find id
    Route::post('/forgot/password', 'AuthenticateController@forgotPassword')->name('.auth.forgot.password'); // for find pwd
    Route::post('/update/password', 'AuthenticateController@changePassword')->name('.update.password'); // for find pwd

    Route::group(['middleware' => ['jwt.verify']], function ($api) {
        Route::post('/logout', 'AuthenticateController@logOut')->name('.auth.logout');

        // Group Chat
        Route::post("create-group", "GroupChatController@createGroup");
        Route::post("group-list", "GroupChatController@listGroups");
        Route::post("group-info", "GroupChatController@infoGroup");
        Route::post("join-group", "GroupChatController@joinGroup");
        Route::post("unjoin-group", "GroupChatController@unjoinGroup");
        Route::post("group-details","GroupChatController@groupMembers");
        Route::post("blocked-list-group","GroupChatController@blockedUsers");
        Route::post("unblock-user-group","GroupChatController@unblockUser");
        Route::post("edit-group-info","GroupChatController@editGroupInfo");
        Route::post("give-leader-authority","GroupChatController@giveLeader");
        Route::post("set-group-chat-push","GroupChatController@setPush");
        Route::post('report-user','GroupChatController@reportUser');

        Route::get('list-keywords','GroupChatController@keywordList');

        Route::get("user-profile","UserController@viewProfile");
        Route::post("user/request-delete", "UserController@deleteMySelf");

        Route::get('categories', "CategoryController@list");
        Route::post('category-posts', "CategoryController@postList");

        Route::post('home','HomeController@index');

        //News websites data
        Route::post('search-news','HomeController@searchNews');
        Route::post('search-more-news','HomeController@searchMoreNews');
        Route::post('view-news-post','HomeController@viewNewsPost');
        Route::post('report-news-post','HomeController@reportNewsPost');

        //Private chat
        Route::post('private-chat-list','PrivateChatController@listChat');
        Route::post('set-private-chat-push','PrivateChatController@setPush');

        Route::get('advance-settings','HomeController@advanceSettings');
    });
});

Route::get('scrape-edaily-news', function (){
    return scrape_edaily_news("chat gpt");
});

Route::get('scrape-etoday-news', function (){
    return scrape_etoday_news("chat gpt");
});

Route::get('scrape-fnnews-news', function (){
    return scrape_fnnews_news("chat gpt");
});

Route::get('scrape-hankyung-news', function (){
    return scrape_hankyung_news("chat gpt");
});

Route::get('scrape-heraldcorp-news', function (){
    return scrape_heraldcorp_news("chat gpt");
});

Route::get('scrape-joseilbo-news', function (){
   return scrape_joseilbo_news("chat gpt");
});

Route::get('scrape-mk-news', function (){
    return scrape_mk_news("chat gpt");
});

Route::get('scrape-mt-news', function (){
    return scrape_mt_news("chat gpt");
});

Route::get('scrape-newspim-news', function (){
    return scrape_newspim_news("chat gpt");
});
