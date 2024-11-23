<?php

namespace App\Traits;

use Cache;
use Illuminate\Http\UploadedFile;
// use Illuminate\Support\Facades\Response;

trait ResponseTrait
{
    public function sendSuccessResponse($message = "", $code = 200, $data = null, $other_data = null)
    {
        $jsonData = array();
        $jsonData['data'] = $data;
        $jsonData['other_data'] = $other_data;
        $jsonData['message'] = $message;
        $jsonData['status_code'] = $code;
        return response()->json($jsonData, $code);
    }

    public function sendFailedResponse($message = "", $code = 400, $data = null, $other_data = null)
    {
        $jsonData = array();
        $jsonData['data'] = $data;
        $jsonData['other_data'] = $other_data;
        $jsonData['message'] = $message;
        $jsonData['status_code'] = $code;
        return response()->json($jsonData, $code);
    }

    public function sendCustomErrorMessage($message = array(), $code = 422, $data = null, $other_data = null)
    {
        $jsonData = [];
        $errors = '';
        foreach ($message as $key => $error) {
            $errors = $error[0];
            break;
        }
        $jsonData['data'] = $data;
        $jsonData['other_data'] = $other_data;
        $jsonData['message'] = $errors;
        $jsonData['status_code'] = $code;
        return response()->json($jsonData, $code);
    }

    public function removeTokenUpdatedCache($user_id)
    {
        if (Cache::get(sprintf(config('kickavenue.cache.token_updated'), $user_id)) === true) {
            return Cache::forget(sprintf(config('kickavenue.cache.token_updated'), $user_id));
        }
        return false;
    }

    public function addTokenUpdatedCache($user_id)
    {
        if (!Cache::get(sprintf(config('kickavenue.cache.token_updated'), $user_id))) {
            return Cache::forever(sprintf(config('kickavenue.cache.token_updated'), $user_id), true);
        }
        return false;
    }

    public function uploadOne(UploadedFile $uploadedFile, $folder = null, $disk = 'public', $filename = null)
    {
        $name = !is_null($filename) ? $filename : rand(25);

        $file = $uploadedFile->storeAs($folder, $name . '.' . $uploadedFile->getClientOriginalExtension(), $disk);

        return $file;
    }

}
