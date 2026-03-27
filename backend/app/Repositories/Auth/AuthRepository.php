<?php

namespace App\Repositories\Auth;

use App\Models\OtpCode;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AuthRepository
{
    public function createOtp(string $phone, string $code): OtpCode
    {
        return OtpCode::create([
            'phone' => $phone,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(5),
            'sent_at' => Carbon::now(),
        ]);
    }

    public function latestOtpByPhone(string $phone): ?OtpCode
    {
        return OtpCode::query()
            ->where('phone', $phone)
            ->latest('id')
            ->first();
    }

    public function findCustomerRole(): Role
    {
        return Role::query()->where('slug', 'customer')->firstOrFail();
    }

    public function findOrCreateCustomer(string $phone, string $name = null): User
    {
        $role = $this->findCustomerRole();

        return User::query()->firstOrCreate(
            ['phone' => $phone],
            [
                'name' => $name ?? null,
                'role_id' => $role->id,
                'phone_verified_at' => Carbon::now(),
            ]
        );
    }

    public function updateProfile(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data): User {
            $user->fill($data);
            if ($user->profile_completed_at === null) {
                $user->profile_completed_at = Carbon::now();
            }
            $user->save();

            return $user->fresh()->load('role');
        });
    }
}
