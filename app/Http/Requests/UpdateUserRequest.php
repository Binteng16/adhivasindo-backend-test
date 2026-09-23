<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')?->id ?? $this->route('user');

        return [
            'name'     => ['nullable', 'string', 'max:255'],
            'email'    => ['nullable', 'email', 'unique:users,email,'.$userId],
            'password' => ['nullable', 'string', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string'    => 'Kolom nama harus berupa teks.',
            'name.max'       => 'Nama tidak boleh melebihi 255 karakter.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Alamat email ini sudah digunakan oleh akun lain.',
            'password.min'   => 'Password baru minimal harus terdiri dari 6 karakter.',
        ];
    }
}
