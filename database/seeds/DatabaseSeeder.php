<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this->call(CountryTableSeeder::class);
        $this->call(StateTableSeeder::class);
//        $this->call(CityTableSeeder::class);
        $this->call(CardsTableSeeder::class);
//        $this->call(DefaultCardsTableSeeder::class);
//        $this->call(DefaultCardsRivTableSeeder::class);
        $this->call(LevelSeeder::class);
//        $this->call(ReportTypeTableSeeder::class);
//        $this->call(CategoryTypeTableSeeder::class);
        $this->call(DeviceTypeTableSeeder::class);
        $this->call(EntityTypeTableSeeder::class);
//        $this->call(PackagePlanTableSeeder::class);
//        $this->call(RequestFormStatusTableSeeder::class);
//        $this->call(RequsetBookingStatusTableSeeder::class);
//        $this->call(SavedHistoryTypeTableSeeder::class);
//        $this->call(ShopImageTypeTableSeeder::class);
        $this->call(StatusTableSeeder::class);
//        $this->call(RecycleOptionTableSeeder::class);
//        $this->call(CategoryTableSeeder::class);
        $this->call(PermissionTableSeeder::class);
//        $this->call(CreditPlanTableSeeder::class);
        $this->call(ConfigTableSeeder::class);
        $this->call(TimezoneTableSeeder::class);
//        $this->call(BasicMentionTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(MobileUsersTableSeeder::class);
//        $this->call(PostLanguageTableSeeder::class);
//        $this->call(DiscountConditionTableSeeder::class);
//        $this->call(MetalkOptionsSeed::class);
//        $this->call(MenuSettingSeeder::class);
//        $this->call(GeneralSettingsSeeder::class);
//        $this->call(CardLevelSeeder::class);
        $this->call(NewsWebsiteTableSeeder::class);
        $this->call(AdvanceTableSeeder::class);
        Model::reguard();

    }
}
