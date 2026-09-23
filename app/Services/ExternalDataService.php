<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalDataService
{
    protected string $sourceUrl = 'https://bit.ly/48ejMhW';

    
    public function getRawData(): array
    {
        return Cache::remember('external_raw_data', 60, function () {
            try {
                $response = Http::withoutVerifying()
                    ->timeout(15)
                    ->get($this->sourceUrl);

                if (! $response->successful()) {
                    Log::error('Gagal mengambil data eksternal, HTTP status: ' . $response->status());
                    return [];
                }

                $json = $response->json();
                $rawString = $json['DATA'] ?? null;

                if (empty($rawString) || ! is_string($rawString)) {
                    return [];
                }

                $lines = preg_split('/\r\n|\r|\n/', trim($rawString));
                if (empty($lines) || count($lines) <= 1) {
                    return [];
                }

                $headerLine = array_shift($lines);
                $headers = array_map('trim', explode('|', $headerLine));

                $parsedData = [];
                foreach ($lines as $line) {
                    $trimmedLine = trim($line);
                    if ($trimmedLine === '') {
                        continue;
                    }

                    $columns = array_map('trim', explode('|', $trimmedLine));

                    if (count($columns) === count($headers)) {
                        $parsedData[] = array_combine($headers, $columns);
                    }
                }

                return $parsedData;
            } catch (Exception $e) {
                Log::error('Terjadi kesalahan saat memproses data eksternal: ' . $e->getMessage());
                return [];
            }
        });
    }

    public function searchByNama(?string $nama = 'Turner Mia'): array
    {
        if (empty(trim((string) $nama))) {
            return [];
        }

        $query = strtolower(trim((string) $nama));

        return collect($this->getRawData())
            ->filter(fn ($item) => strcasecmp(trim($item['NAMA'] ?? ''), $query) === 0)
            ->values()
            ->all();
    }

    public function searchByNim(?string $nim = '9352078461'): array
    {
        if (empty(trim((string) $nim))) {
            return [];
        }

        $query = trim((string) $nim);

        return collect($this->getRawData())
            ->filter(fn ($item) => trim((string) ($item['NIM'] ?? '')) === $query)
            ->values()
            ->all();
    }

    public function searchByYmd(?string $ymd = '20230405'): array
    {
        if (empty(trim((string) $ymd))) {
            return [];
        }

        $query = trim((string) $ymd);

        return collect($this->getRawData())
            ->filter(fn ($item) => trim((string) ($item['YMD'] ?? '')) === $query)
            ->values()
            ->all();
    }
}
