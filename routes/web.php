<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Auth::routes();

Route::get('/', function () {
    return redirect(route('login'));
});

Route::get('/admin', function () {
    return redirect(route('admin.dashboard.index'));
});

Route::get('/home', function () {
    return redirect(route('admin.dashboard.index'));
});

Route::name('admin.')->prefix('admin')->middleware(['auth', 'authlocalization'])->group(function () {
    Route::get('dashboard', 'Admin\DashboardController@index')->name('dashboard.index');

    Route::post('check/user/unread-comments', 'Admin\ThemeSectionController@checkUserUnreadComments')->name('check.user.unread-comment');
    Route::post('user/update/language/{id}', 'Admin\DashboardController@updateLanguage')->name('user.update.language');

    //links page in important settings
    Route::get('important-setting/links', 'Admin\ImportantSettingController@indexLinks')->name('important-setting.links.index');
    Route::post('important-setting/links/table', 'Admin\ImportantSettingController@getJsonLinksData')->name('important-setting.links.table');
    Route::get('important-setting/links/edit/{id}', 'Admin\ImportantSettingController@editLinks')->name('important-setting.links.edit');
    Route::put('important-setting/links/update/{id}', 'Admin\ImportantSettingController@updateLinks')->name('important-setting.links.update');

    //Policy pages
    Route::get('important-setting/policy-pages', 'CMSPagesController@index')->name('important-setting.policy-pages.index');
    Route::post('important-setting/policy-pages/list', 'CMSPagesController@getJsonData')->name('policy-pages.get.data');
    Route::get('important-setting/policy-pages/create', 'CMSPagesController@create')->name('policy-pages.create');
    Route::post('policy-pages/store', 'CMSPagesController@store')->name('policy-pages.store');
    Route::post('policy-pages/update/{id}', 'CMSPagesController@update')->name('policy-pages.update');
    Route::post('/page/ckeditor/upload', 'CMSPagesController@uploadImage')->name('ckeditor.upload');
    Route::get('important-setting/policy-pages/edit/{page}', 'CMSPagesController@edit')->name('policy-pages.edit');
    Route::get('policy-pages/get/delete/{id}', 'CMSPagesController@getDelete')->name('policy-pages.get.delete');
    Route::post('policy-pages/delete', 'CMSPagesController@deletePage')->name('policy-pages.delete');

    //Advance
    Route::get('important-setting/advance', 'Admin\ImportantSettingController@indexAdvance')->name('important-setting.advance.index');
    Route::post('important-setting/advance/table', 'Admin\ImportantSettingController@getJsonAdvanceData')->name('important-setting.advance.table');
    Route::get('important-setting/advance/edit/{id}', 'Admin\ImportantSettingController@editAdvance')->name('important-setting.advance.edit');
    Route::put('important-setting/advance/update/{id}', 'Admin\ImportantSettingController@updateAdvance')->name('important-setting.advance.update');

    //Review to delete account
    Route::get('reasons-delete-account', 'Admin\DeleteAccountReasonController@index')->name('reasons-delete-account.index');
    Route::post('reasons-delete-account/table', 'Admin\DeleteAccountReasonController@getJsonData')->name('reasons-delete-account.table');
    Route::get('reasons-delete-account/delete/{id}', 'Admin\DeleteAccountReasonController@delete')->name('reasons-delete-account.delete');
    Route::delete('reasons-delete-account/destroy/{id}', 'Admin\DeleteAccountReasonController@destroy')->name('reasons-delete-account.destroy');

    //Keyword
    Route::get('keyword', 'Admin\KeywordController@index')->name('keyword.index');
    Route::get('keyword/add', 'Admin\KeywordController@addKeyword')->name('keyword.add');
    Route::post('keyword/store', 'Admin\KeywordController@storeKeyword')->name('keyword.store');
    Route::post('keyword/table', 'Admin\KeywordController@getJsonData')->name('keyword.table');
    Route::post('keyword/update/order', 'Admin\KeywordController@updateOrder')->name('keyword.update.order');
    Route::get('keyword/edit/{id}', 'Admin\KeywordController@editKeyword')->name('keyword.edit');
    Route::put('keyword/update/{id}', 'Admin\KeywordController@updateKeyword')->name('keyword.update');

    //Category
    Route::get('category', 'Admin\CategoryController@index')->name('category.index');
    Route::post('category/table', 'Admin\CategoryController@getJsonData')->name('category.table');
    Route::post('category/update/order', 'Admin\CategoryController@updateOrder')->name('category.update.order');
    Route::get('category/add', 'Admin\CategoryController@addCategory')->name('category.add');
    Route::post('category/store', 'Admin\CategoryController@storeCategory')->name('category.store');
    Route::get('category/edit/{id}', 'Admin\CategoryController@editCategory')->name('category.edit');
    Route::put('category/update/{id}', 'Admin\CategoryController@updateCategory')->name('category.update');

    //Main page post
    Route::get('category-posts', 'Admin\CategoryPostController@index')->name('category-posts.index');
    Route::get('category-posts/add', 'Admin\CategoryPostController@addCategoryPost')->name('category-posts.add');
    Route::post('category-posts/store', 'Admin\CategoryPostController@storeCategoryPost')->name('category-posts.store');
    Route::post('category-posts/table', 'Admin\CategoryPostController@getJsonData')->name('category-posts.table');
    Route::post('category-posts/update/pin-to-top', 'Admin\CategoryPostController@updatePintotop')->name('category-posts.update.pin-to-top');

    //User
    Route::get('users', 'Admin\UserController@index')->name('user.index');
    Route::post('users/table/all', 'Admin\UserController@getJsonAllData')->name('user.all.table');
    Route::get('users/get/edit/username/{id}', 'Admin\UserController@getEditUsername')->name('user.get-edit-username');
    Route::post('users/edit/username/{id}', 'Admin\UserController@editUsername')->name('user.edit-username');
    Route::post('users/add/signup-code', 'Admin\UserController@addSignupCode')->name('user.signup-code.save');
    Route::get('show/referral/users/{id}', 'Admin\UserController@showRefferalUser')->name('show.referral.user');
    Route::get('users/get/account/{id}', 'Admin\UserController@getAccount')->name('user.get-account');
    Route::post('delete/user/details', 'Controller@deleteUserDetails')->name('delete.user.details');
    Route::get('users/get/edit/account/{id}', 'Admin\UserController@getEditAccount')->name('user.get-edit-account');
    Route::post('users/edit/account/{id}', 'Admin\UserController@editAccount')->name('user.edit-account');
    Route::get('users/get/edit/email/{id}', 'Admin\UserController@getEditEmail')->name('user.get-edit-email');
    Route::post('users/edit/email/{id}', 'Admin\UserController@editEmailAddress')->name('user.edit-email');
    Route::get('users/create', 'Admin\UserController@createUser')->name('user.create');
    Route::post('users/store', 'Admin\UserController@storeUser')->name('users.store');

    //News websites
    Route::get('news-websites', 'Admin\NewsWebsiteController@index')->name('news-websites.index');
    Route::post('news-websites/table', 'Admin\NewsWebsiteController@getJsonData')->name('news-websites.table');
    Route::post('news-websites/update/order', 'Admin\NewsWebsiteController@updateOrder')->name('news-websites.update.order');

    //Group chat list
    Route::get('group-chat', 'Admin\GroupChatController@index')->name('group-chat.index');
    Route::post('group-chat/table', 'Admin\GroupChatController@getJsonData')->name('group-chat.table');
    Route::get('group-chat/show-messages/{id}', 'Admin\GroupChatController@showMessages')->name('show.group-chat.messages');

    Route::get('reported-post', 'Admin\ReportedNewsPostController@index')->name('reported-post.index');
    Route::post('reported-post/table', 'Admin\ReportedNewsPostController@getJsonData')->name('reported-post.table');
    Route::get('reported-post/get/post/{id}', 'Admin\ReportedNewsPostController@getPost')->name('reported-post.get-post');
    Route::post('reported-post/block-post', 'Admin\ReportedNewsPostController@blockPost')->name('reported-post.block.post');

    Route::get('reported-users', 'Admin\ReportedUserController@index')->name('reported-users.index');
    Route::post('reported-users/table', 'Admin\ReportedUserController@getJsonData')->name('reported-users.table');
});

Route::get('pages/{slug}', 'CMSPagesController@viewPages')->name('page.view');
Route::get('support', 'CMSPagesController@supportPage')->name('support-page.view');

