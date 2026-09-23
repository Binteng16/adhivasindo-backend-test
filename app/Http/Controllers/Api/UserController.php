<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected UserService $userService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $limit = $request->query('limit') ? (int) $request->query('limit') : null;
            $users = $this->userService->getAllUsers($limit);

            return $this->successResponse(
                data: UserResource::collection($users),
                message: $users->isEmpty()
                    ? 'Belum ada data user yang terdaftar.'
                    : 'Daftar user berhasil diambil.',
                meta: [
                    'total' => $users->count(),
                    'limit' => $limit,
                ]
            );
        } catch (Throwable $e) {
            Log::error('Gagal mengambil daftar user: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(
                message: 'Terjadi kesalahan sistem saat mengambil data user.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->createUser($request->validated());

            return $this->successResponse(
                data: new UserResource($user),
                message: 'User baru berhasil ditambahkan.',
                statusCode: Response::HTTP_CREATED
            );
        } catch (Throwable $e) {
            Log::error('Gagal menambahkan user baru: ' . $e->getMessage(), [
                'payload' => $request->safe()->except(['password']),
                'trace'   => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(
                message: 'Terjadi kesalahan sistem saat menyimpan user baru.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $user = $this->userService->findUserById($id);

            if (! $user) {
                return $this->errorResponse(
                    message: 'Data user tidak ditemukan.',
                    statusCode: Response::HTTP_NOT_FOUND
                );
            }

            return $this->successResponse(
                data: new UserResource($user),
                message: 'Detail data user berhasil ditemukan.'
            );
        } catch (Throwable $e) {
            Log::error("Gagal mengambil detail user ID {$id}: " . $e->getMessage(), [
                'id'    => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(
                message: 'Terjadi kesalahan sistem saat mengambil detail user.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        try {
            $user = $this->userService->findUserById($id);

            if (! $user) {
                return $this->errorResponse(
                    message: 'Data user tidak ditemukan.',
                    statusCode: Response::HTTP_NOT_FOUND
                );
            }

            $updatedUser = $this->userService->updateUser($user, $request->validated());

            return $this->successResponse(
                data: new UserResource($updatedUser),
                message: 'Data user berhasil diperbarui.'
            );
        } catch (Throwable $e) {
            Log::error("Gagal memperbarui user ID {$id}: " . $e->getMessage(), [
                'id'      => $id,
                'payload' => $request->safe()->except(['password']),
                'trace'   => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(
                message: 'Terjadi kesalahan sistem saat memperbarui data user.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $user = $this->userService->findUserById($id);

            if (! $user) {
                return $this->errorResponse(
                    message: 'Data user tidak ditemukan.',
                    statusCode: Response::HTTP_NOT_FOUND
                );
            }

            $this->userService->deleteUser($user);

            return $this->successResponse(
                data: null,
                message: 'Data user berhasil dihapus dari sistem.'
            );
        } catch (Throwable $e) {
            Log::error("Gagal menghapus user ID {$id}: " . $e->getMessage(), [
                'id'    => $id,
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(
                message: 'Terjadi kesalahan sistem saat menghapus data user.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
