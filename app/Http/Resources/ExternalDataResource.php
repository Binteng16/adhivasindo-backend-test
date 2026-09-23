<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExternalDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'nama' => $this->resource['NAMA'] ?? null,
            'nim'  => $this->resource['NIM'] ?? null,
            'ymd'  => $this->resource['YMD'] ?? null,
        ];
    }
}
