<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected UserService $userService
    ) {}

    public function index(): JsonResponse
    {
        $users = $this->userService->getAllUsers();

        return $this->successResponse(
            data: UserResource::collection($users),
            message: $users->isEmpty()
                ? 'Belum ada data user yang terdaftar.'
                : 'Daftar user berhasil diambil.',
            meta: [
                'total' => $users->count(),
            ]
        );
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->validated());

        return $this->successResponse(
            data: new UserResource($user),
            message: 'User baru berhasil ditambahkan.',
            statusCode: Response::HTTP_CREATED
        );
    }

    public function show(string $id): JsonResponse
    {
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
    }

    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $user = $this->userService->findUserById($id);

        if (! $user) {
            return $this->errorResponse(
                message: 'Data user tidak ditemukan.',
                statusCode: Response::HTTP_NOT_FOUND
            );
        }

        // Service akan mem-filter data mana saja yang valid dan patut diubah
        $updatedUser = $this->userService->updateUser($user, $request->validated());

        return $this->successResponse(
            data: new UserResource($updatedUser),
            message: 'Data user berhasil diperbarui.'
        );
    }

    public function destroy(string $id): JsonResponse
    {
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
    }
}
