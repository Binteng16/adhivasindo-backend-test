<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExternalDataResource;
use App\Services\ExternalDataService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExternalSearchController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected ExternalDataService $dataService
    ) {}

    public function searchNama(Request $request): JsonResponse
    {
        $target = $request->query('nama', $request->query('query', 'Turner Mia'));

        return $this->handleSearch(
            field: 'NAMA',
            value: $target,
            searchCallback: fn (string $query) => $this->dataService->searchByNama($query)
        );
    }

    public function searchNim(Request $request): JsonResponse
    {
        $target = $request->query('nim', $request->query('query', '9352078461'));

        return $this->handleSearch(
            field: 'NIM',
            value: $target,
            searchCallback: fn (string $query) => $this->dataService->searchByNim($query)
        );
    }

    public function searchYmd(Request $request): JsonResponse
    {
        $target = $request->query('ymd', $request->query('query', '20230405'));

        return $this->handleSearch(
            field: 'YMD',
            value: $target,
            searchCallback: fn (string $query) => $this->dataService->searchByYmd($query)
        );
    }

    private function handleSearch(string $field, mixed $value, callable $searchCallback): JsonResponse
    {
        $trimmedValue = trim((string) $value);

        if ($trimmedValue === '') {
            return $this->errorResponse(
                message: "Parameter {$field} tidak boleh kosong.",
                statusCode: Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $results = $searchCallback($trimmedValue);
        $count = count($results);

        return $this->successResponse(
            data: ExternalDataResource::collection($results),
            message: $count > 0
                ? 'Data berhasil ditemukan.'
                : "Data dengan kriteria {$field} tersebut tidak ditemukan.",
            meta: [
                'field'       => $field,
                'query'       => $trimmedValue,
                'total_found' => $count,
                'timestamp'   => now()->toISOString(),
            ]
        );
    }
}
