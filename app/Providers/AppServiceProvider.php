<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;
// use App\Providers\Braintree_Configuration;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $braintree = new \Braintree\Gateway([
            'environment' => env('BRAINTREE_ENV','sandbox'),
            'merchantId' => env('BRAINTREE_MERCHANT_ID'),
            'publicKey' => env('BRAINTREE_PUBLIC_KEY'),
            'privateKey' => env('BRAINTREE_PRIVATE_KEY')
        ]);
        config(['braintree' => $braintree]); 

        /** video duration validation  'video:25' */
        Validator::extend('video_length', function($attribute, $value, $parameters, $validator) {
            // validate the file extension
            if(!empty($value->getClientOriginalExtension())){
                    $getID3 = new \getID3;
                    $file = $getID3->analyze($value->getRealPath());
                    $duration = $file['playtime_seconds'];
                   
                return(round($duration) > ($parameters[0] + 0)) ? false:true;
            }else{
                return false;
            }
        });

        if(config('app.env') === 'production') {
            \URL::forceScheme('https');
        }
    }
    
}
