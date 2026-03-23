<?php

namespace App\Domain\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class HiddenInspectionScope implements Scope
{
    /**
     * Apply the scope to all queries on Inspection model.
     * Super Admin sees everything. Others see only non-hidden inspections.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        // Not logged in, or not super admin → hide hidden inspections
        if (!$user || !$user->hasRole('Super Admin')) {
            $builder->where('is_hidden', false);
        }
    }
}