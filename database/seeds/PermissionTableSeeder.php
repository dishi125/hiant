<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $permissions = [
            'top-post-list',
            'user-list',
            'business-client-list',
            'requested-client-list',
            'reported-client-list',
            'suggest-custom-list',
            'category-list',
            'currency-coin-list',
            'reload-coin-list',
            'manager-list',
            'role-list',
            'announcement-list',
            'important-custom-list',
            'reward-instagram-list',
            'activity-log-list',
            'my-business-client-list',
            'community-list',
            'comment-list',
            'check-bad-complete-list',
            'review-list',
            'association-list',
            'cards-list',
            'outside-user-list',
            'wedding-list',
            'requested-card-list'
         ];

        foreach ($permissions as $permission) {
            Permission::firstOrcreate(['name' => $permission]);
        }

        $roles = [
            ['name' => 'Admin',  'display_name' => 'Admin'],
            ['name' => 'Manager', 'display_name' => 'Company'],
            ['name' => 'Sub Manager', 'display_name' => 'Supporter'],
        ];

        foreach ($roles as $role) {
            $newRole = Role::firstOrCreate(['name' => $role['name'],'display_name' => $role['display_name']]);
            if($role['name'] == 'Admin') {
                $newRole->syncPermissions(Permission::all());
            }
         }
    }
}
