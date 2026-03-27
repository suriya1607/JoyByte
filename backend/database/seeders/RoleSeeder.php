<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Customer', 'slug' => 'customer'],
            ['name' => 'Admin', 'slug' => 'admin'],
            ['name' => 'Shop Owner', 'slug' => 'shop_owner'],
            ['name' => 'Delivery Agent', 'slug' => 'delivery_agent'],
        ];

        foreach ($roles as $role) {
            Role::query()->updateOrCreate(
                ['slug' => $role['slug']],
                ['name' => $role['name']]
            );
        }
    }
}
