<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Country;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\EntityTypes;
use App\Models\UserDetail;
use App\Models\Status;
use App\Models\UserEntityRelation;

class MobileUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'name' => 'User 1',
                'username' => 'user1@gmail.com',
                'email' => 'user1@gmail.com',
                'password' => '123456',
                'phone' => 123456789,
                'phone_code' => 91,
                'gender' => 'Female',
                'device_type_id' => 1,
                'device_id' => 11122,
                'device_token' => "test7890",
                'entity_type_id' => EntityTypes::NORMALUSER,
                'status_id' => 1,
                'role' => 'Normal User',
            ],
            [
                'name' => 'User 2',
                'username' => 'user2@gmail.com',
                'email' => 'user2@gmail.com',
                'password' => '123456',
                'phone' => 123456789,
                'phone_code' => 91,
                'gender' => 'Male',
                'device_type_id' => 1,
                'device_id' => 11122,
                'device_token' => "test7890",
                'entity_type_id' => EntityTypes::NORMALUSER,
                'status_id' => 1,
                'role' => 'Normal User',
            ],
            [
                'name' => 'User 3',
                'username' => 'user3@gmail.com',
                'email' => 'user3@gmail.com',
                'password' => '123456',
                'phone' => 123456789,
                'phone_code' => 91,
                'gender' => 'Female',
                'device_type_id' => 1,
                'device_id' => 11122,
                'device_token' => "test7890",
                'entity_type_id' => EntityTypes::NORMALUSER,
                'status_id' => 1,
                'role' => 'Normal User',
            ],

        ];

        foreach ($items as $item) {
            $country = Country::where('phonecode', $item['phone_code'])->first();

            $user = User::create([
                "email" => $item['email'],
                'username' => $item['email'],
                "password" => Hash::make($item['password']),
                'status_id' => Status::ACTIVE,
            ]);

            UserEntityRelation::create(['user_id' => $user->id,"entity_type_id" => EntityTypes::NORMALUSER,'entity_id' => $user->id]);

            $member = UserDetail::create([
                'user_id' => $user->id,
                'country_id' => $country->id,
                'name' => $item['name'],
                'mobile' => $item['phone'],
                'gender' => $item['gender'],
                'device_type_id' => $item['device_type_id'],
                'device_id' => $item['device_id'],
                'device_token' => $item['device_token'],
                'recommended_code' => bin2hex(random_bytes(3)),
            ]);

            $role = Role::firstOrCreate(['name' => $item['role']]);
            $user->assignRole([$role->id]);
        }
    }
}
