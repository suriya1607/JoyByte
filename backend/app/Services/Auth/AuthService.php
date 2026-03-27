<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\Auth\AuthRepository;
use App\Services\Media\InteractMediaUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private readonly AuthRepository $authRepository,
        private readonly InteractMediaUploadService $mediaUploadService
    )
    {
    }

    public function sendOtp(string $phone): array
    {
        $code = App::environment('production') ? (string) random_int(100000, 999999) : '123456';
        $this->authRepository->createOtp($phone, $code);

        return [
            'phone' => $phone,
            'expires_in_seconds' => 300,
            'otp_hint' => App::environment('production') ? null : $code,
        ];
    }

    public function verifyOtp(string $phone, string $otp): array
    {
        $otpCode = $this->authRepository->latestOtpByPhone($phone);

        if (! $otpCode) {
            throw ValidationException::withMessages([
                'phone' => ['OTP not found for this phone number.'],
            ]);
        }

        if ($otpCode->isExpired()) {
            throw ValidationException::withMessages([
                'otp' => ['OTP is expired. Please request a new OTP.'],
            ]);
        }

        if ($otpCode->verified_at !== null || $otpCode->code !== $otp) {
            throw ValidationException::withMessages([
                'otp' => ['Invalid OTP code.'],
            ]);
        }

        $otpCode->markVerified();
        $user = $this->authRepository->findOrCreateCustomer($phone);
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('role'),
        ];
    }

    public function updateProfile(User $user, array $payload, ?UploadedFile $avatar): User
    {
        $data = [
            'name' => $payload['name'],
            'email' => $payload['email'] ?? null,
        ];

        if ($avatar) {
            $data['avatar_path'] = $this->mediaUploadService->uploadUserAvatar($avatar, $user->avatar_path);
        }

        return $this->authRepository->updateProfile($user, $data);
    }
}
