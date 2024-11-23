<?php

namespace App\Http\Controllers\Admin;

use App\Models\DeleteAccountReason;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ThemeSectionController extends Controller
{
    public function checkUserUnreadComments(Request $request)
    {
        $isUnread = false;

        try{
            $userCount = DB::table('users')->whereNull('deleted_at')->where('is_admin_read',1)->get('id');
            $deleteReasonCount = DeleteAccountReason::where('is_admin_read',1)->get();

            $jsonData = array(
                'success' => true,
                "user_unread_count" => count($userCount),
                "reasons_delete_account_unread_count" => count($deleteReasonCount),
            );
            return response()->json($jsonData);
        } catch (\Exception $ex) {
            $jsonData = array(
                'success' => false,
                'user_unread_count' => 0,
                "reasons_delete_account_unread_count" => 0,
            );
            return response()->json($jsonData);
        }
    }

}
