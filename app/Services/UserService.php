<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{

    public function getAllUsers(): Collection
    {
        return User::latest()->get();
    }

    public function findUserById(string $id): ?User
    {
        return User::find($id);
    }

    public function createUser(array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return User::create($data);
    }

    public function updateUser(User $user, array $data): User
    {
        $cleanPayload = array_filter($data, fn ($value) => ! is_null($value) && $value !== '');

        if (! empty($cleanPayload)) {
            if (isset($cleanPayload['password'])) {
                $cleanPayload['password'] = Hash::make($cleanPayload['password']);
            }

            $user->update($cleanPayload);
        }

        return $user->fresh();
    }

    public function deleteUser(User $user): bool
    {
        return (bool) $user->delete();
    }
}
