<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Http\Responses\ApiResponse;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $result = $this->authService->sendOtp($request->validated('phone'));

        return ApiResponse::success($result, 'OTP sent successfully.');
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $result = $this->authService->verifyOtp(
            $request->validated('phone'),
            $request->validated('otp')
        );

        return ApiResponse::success($result, 'OTP verified successfully.');
    }

    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success(
            $request->user()?->load('role'),
            'Authenticated user fetched.'
        );
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->authService->updateProfile(
            $request->user(),
            $request->validated(),
            $request->file('avatar')
        );

        return ApiResponse::success($user, 'Profile updated successfully.');
    }
}
