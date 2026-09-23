<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthResource;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected AuthService $authService
    ) {}

    public function login(Request $request): JsonResponse
    {
        $fields = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'Kolom email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Kolom password wajib diisi.',
        ]);

        $authResult = $this->authService->attemptLogin(
            email: $fields['email'],
            password: $fields['password']
        );

        if (! $authResult) {
            return $this->errorResponse(
                message: 'Email atau password yang Anda masukkan salah.',
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        }

        return $this->successResponse(
            data: new AuthResource($authResult['user'], $authResult['token']),
            message: 'Login berhasil.',
            statusCode: Response::HTTP_OK
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->successResponse(
            data: null,
            message: 'Berhasil logout'
        );
    }
}
