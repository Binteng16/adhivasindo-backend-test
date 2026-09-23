<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    protected string $token;

    public function __construct($resource, string $token)
    {
        parent::__construct($resource);
        $this->token = $token;
    }

    /**
     * Transformasikan token dan data profil login ke bentuk array.
     */
    public function toArray(Request $request): array
    {
        return [
            'token_type'   => 'Bearer',
            'access_token' => $this->token,
            'user'         => [
                'id'    => $this->id,
                'name'  => $this->name,
                'email' => $this->email,
                'created_at' => $this->created_at?->toISOString(),
            ],
        ];
    }
}
