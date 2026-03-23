<?php

namespace App\Application\Services;

use App\Domain\Models\AuditLog;
use App\Domain\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return User::with('roles')
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'Super Admin'))
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function find(string $id): User
    {
        return User::with('roles')->findOrFail($id);
    }

    public function create(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        if (!empty($data['role'])) {
            $user->assignRole($data['role']);
        }

        AuditLog::log('user_created', User::class, $user->id);

        return $user;
    }

    public function update(string $id, array $data): User
    {
        $user = User::findOrFail($id);

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        if (!empty($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        AuditLog::log('user_updated', User::class, $id);

        return $user->fresh('roles');
    }

    public function delete(string $id): bool
    {
        AuditLog::log('user_deleted', User::class, $id);
        return User::findOrFail($id)->delete();
    }

    public function toggleActive(string $id): User
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);
        return $user->fresh();
    }

    public function getInspectors()
    {
        return User::role('Inspector')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}