<?php

namespace App\Policies;

use App\Domain\Models\Inspection;
use App\Domain\Models\User;

class InspectionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Inspector', 'Viewer']);
    }

    public function view(User $user, Inspection $inspection): bool
    {
        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Viewer'])) {
            return true;
        }

        return $user->id === $inspection->inspector_id || $user->id === $inspection->created_by;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Inspector']);
    }

    public function conduct(User $user, Inspection $inspection): bool
    {
        if ($user->hasAnyRole(['Super Admin', 'Admin'])) {
            return true;
        }

        if (!$user->hasRole('Inspector')) {
            return false;
        }
        return $user->id === $inspection->inspector_id
            || $user->id === $inspection->created_by
            || $inspection->inspector_id === null;
    }

    public function cancel(User $user, Inspection $inspection): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }

    public function delete(User $user, Inspection $inspection): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }
}