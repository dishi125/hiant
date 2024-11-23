<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Config extends Model
{
    use SoftDeletes;
    protected $table = 'config';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'key', 'value', 'is_link', 'created_at', 'updated_at', 'sort_order', 'is_different_lang', 'is_show_hide'
    ];

    protected $casts = [
        'key' => 'string',
        // 'value' => 'string',
        'is_link' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    const TOTAL_SHOP_PORTFOLIO_POST = "total_able_shop_portfolio_post";
    const PORTFOLIO_LIMIT_PER_DAY = "portfolio_limit_per_a_day";
    const SPONSOR_POST_LIMIT = "sponsor_post_limit";
    const SHOP_RECOMMEND_MONEY = "shop_recommendcode_money_per_one_recommend";
    const HOSPITAL_RECOMMEND_MONEY = "hospital_recommendcode_money_per_one_recommend";
    const VAT_RATE = "vat_rate";
    const BECAME_SHOP = "became_shop_and_get_credit";
    const BECAME_HOSPITAL = "became_hospital_and_get_credit";
    const BUSINESS_ASKING_EMAIL = "business_asking_e_mail";
    const COMPANY_CONTACT_NO = "company_contact_number";
    const PUSH_GPS = "push_and_gps_agreement";
    const SHOP_PROFILE_ADD_PRICE = "shop_profile_add_price";
    const SNS_REWARD = "sns_reward";
    const SNS_PENALTY = "sns_penalty";
    const SNS_HELP_VIDEO = "sns_help_video";
    const REVIEW_POST_REPORTED_DELETE = "review_post_reported_comment_delete";
    const COMMUNITY_POST_REPORTED_DELETE = "community_post_reported_comment_delete";
    const DEDUCT_AGAIN_AFTER_FIRST_DEDUCT = "deduct_again_from_first_dedect_week";
    const REVERIFY_USER_PHONE_NUMBER_DAYS = "reverify_user_phone_number_after_n_days";
    const USER_INCONVINIENCE_EMAIL = "user_inconvinience_e_mail";
    const SNS_REWARD_VISIBLE_HOURS = "sns_reward_visible_after_n_hours";
    const RELOAD_COIN_ORDER_EMAIL = "reload_coin_order_e_mail";
    const REQUEST_CLIENT_REPORT_SNS_REWARD_EMAIL = "request_client_report_sns_reward_email";
    const CREATE_SHOP_PROFILE_LIMIT = "create_shop_profile_limit";
    const SNS_GET_VIDEO = "sns_get_video";
    const REJOIN_TIME_LIMIT = "leave_and_re_join_time_limit_for_association";
    const ADMIN_PHONE_NUMBER = "admin_phone_number";
    const ADMIN_MASTER_PASSWORD = "master_password";
    const SUGGESTED_CATEGORY = "suggested_category";
    const SHOW_COIN_INFO = "show_coin_info";
    const SHOW_FIXED_COUNTRY = "show_fixed_country";
    const GIVE_REFERRAL_EXP = "give_referral_exp";
    const SHOW_REVIEW_TAB = "show_review_tab";
    const SWITCH_CHARACTER_ONLY_MODE = "switch_character_only_mode";
    const SHOW_FULL_SCREEN_AD = "show_full_screen_ads";
    const SHOW_BANNER_ADS = "show_banner_ads";
    const ONLY_SHOP_MODE = "only_shop_mode";
    const PURCHASE_ORDER_EMAIL = "purchase_order_email";
    const INSTA_CRON_TIME = "insta_cron_time";
    const INSTAGRAM_REGISTER_PUSH_EMAIL = "instagram_register_push_email";
    const PRODUCT_PAYMENT_METHOD = "product_payment_method";
    const PRODUCT_PRICE = "product_price";
    const PRODUCT_NAME = "product_name";
    const SIGNUP_EMAIL = "signup_email";
    const SHOW_RECENT_COMPLETED_SHOPS = "show_recent_completed_shops";
    const LIKE_ORDER = "like_order";
    const REASON_EMAIL = "delete_account_reason";

    const PAYPLE_FIELDS = [
        Config::PRODUCT_PAYMENT_METHOD,
        Config::PRODUCT_PRICE,
        Config::PRODUCT_NAME,
    ];
    //protected $appends = ['original_key'];


    public function getKeyAttribute()
    {
        $value = $this->attributes['key'];
        return $this->attributes['key'] = Str::ucfirst(str_replace('_', ' ', $value));
    }

    public function getFormattedValueAttribute()
    {
        $key = isset($this->attributes['key']) ? $this->attributes['key'] : "";
        $value = isset($this->attributes['value']) ? $this->attributes['value'] : "";
        if ($value) {
            $key_array = [
                Config::SHOP_RECOMMEND_MONEY, Config::SNS_REWARD, Config::SNS_PENALTY,
                Config::HOSPITAL_RECOMMEND_MONEY, Config::BECAME_SHOP, Config::BECAME_HOSPITAL
            ];
            return $this->attributes['formatted_value'] = in_array($key, $key_array) ? number_format($value) : $value;
        }

        return $this->attributes['formatted_value'] = $value;
    }

    static function expirePassword()
    {

        $getMasterPassword = Config::where('key', Config::ADMIN_MASTER_PASSWORD)->first();

        if ($getMasterPassword) {
            $value = $getMasterPassword ? $getMasterPassword->value : NULL;
            $key = $getMasterPassword ? $getMasterPassword->key : NULL;
            $updated_at = $getMasterPassword ? $getMasterPassword->updated_at : NULL;

            if (!empty($value)) {

                $now = Carbon::now();
                $$updated_at = Carbon::parse($updated_at);
                $diffHours = $$updated_at->diffInHours($now);

                if ($diffHours >= 24) {
                    Config::where('key', Config::ADMIN_MASTER_PASSWORD)->update(['value' => NULL]);
                }
            }
        }
    }
}
