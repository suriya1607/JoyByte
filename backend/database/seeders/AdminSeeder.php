<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::query()->where('slug', 'admin')->first();

        if (! $adminRole) {
            return;
        }

        User::query()->updateOrCreate(
            ['phone' => '9999999999'],
            [
                'name' => 'PyTrip Admin',
                'role_id' => $adminRole->id,
                'phone_verified_at' => Carbon::now(),
            ]
        );
    }
}
