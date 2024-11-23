<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Country;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\EntityTypes;
use App\Models\Address;
use App\Models\Status;
use App\Models\UserEntityRelation;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
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
                'name' => 'Admin',
                'username' => 'admin@admin.com',
                'email' => 'admin@admin.com',
                'password' => bcrypt('hiant2134!!'),
                'entity_type_id' => EntityTypes::ADMIN,
                'status_id' => Status::ACTIVE,
                'role' => 'Admin'
            ],
            [
                'name' => 'Admin',
                'username' => 'gwb9160@nate.com',
                'email' => 'gwb9160@nate.com',
                'password' => bcrypt('qwer1234'),
                'entity_type_id' => EntityTypes::ADMIN,
                'status_id' => Status::ACTIVE,
                'role' => 'Admin'
            ],
            [
                'name' => 'Manager',
                'username' => 'manager@manager.com',
                'email' => 'manager@manager.com',
                'password' => bcrypt('concetto@123'),
                'entity_type_id' => EntityTypes::MANAGER,
                'status_id' => Status::ACTIVE,
                'role' => 'Manager'
            ],
            [
                'name' => 'Sub Manager',
                'username' => 'submanager@submanager.com',
                'email' => 'submanager@submanager.com',
                'password' => bcrypt('concetto@123'),
                'entity_type_id' => EntityTypes::SUBMANAGER,
                'status_id' => Status::ACTIVE,
                'role' => 'Sub Manager'
            ],
        ];

        foreach ($items as $item) {
            $newUser = User::firstOrCreate([
                'email' => $item['email'],
                'password' => $item['password'],
                'username' => $item['username'],
                'status_id' => $item['status_id'],
            ]);

            /*$manager = Manager::create([
                'name' => $item['name'],
                'user_id' => $newUser->id,
                'recommended_code' => Str::upper(Str::random(7))
            ]);*/

            UserEntityRelation::create(['user_id' => $newUser->id,"entity_type_id" => $item['entity_type_id'],'entity_id' => $newUser->id]);
            /*if($item['entity_type_id'] == EntityTypes::MANAGER || $item['entity_type_id'] == EntityTypes::SUBMANAGER) {
                $address = Address::create([
                    "entity_type_id" => $item['entity_type_id'],
                    "entity_id" => $newUser->id,
                    "address" => "Abc Road",
                    "country_id" => 101,
                    "state_id" => 12,
                    "city_id" => 783,
                    "main_address" => 1
                ]);
            }*/

            $role = Role::firstOrCreate(['name' => $item['role']]);
            $role->syncPermissions(Permission::all());
            $newUser->assignRole([$role->id]);
        }
    }
}
