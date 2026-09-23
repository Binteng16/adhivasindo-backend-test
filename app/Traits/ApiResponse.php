<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{

    public function successResponse(
        mixed $data = null,
        string $message = 'Operasi berhasil.',
        int $statusCode = Response::HTTP_OK,
        array $meta = []
    ): JsonResponse {
        $payload = [
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ];

        if (! empty($meta)) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $statusCode);
    }

    public function errorResponse(
        string $message = 'Terjadi kesalahan.',
        int $statusCode = Response::HTTP_BAD_REQUEST,
        mixed $errors = null
    ): JsonResponse {
        $payload = [
            'status'  => 'error',
            'message' => $message,
        ];

        if (! is_null($errors)) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $statusCode);
    }
}
