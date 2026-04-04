<?php

namespace App\Domain\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class HiddenInspectionScope implements Scope
{
    /**
     * Super Admin يرى كل شيء.
     * Share route (unauthenticated) يرى الفحوصات غير المخفية فقط.
     * بقية المستخدمين يرون غير المخفية فقط.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // تحقق آمن من auth — لا يكسر في console أو unauthenticated requests
        try {
            $user = auth()->check() ? auth()->user() : null;
        } catch (\Throwable) {
            $user = null;
        }

        if ($user && $user->hasRole('Super Admin')) {
            return; // Super Admin يرى كل شيء
        }

        $builder->where('is_hidden', false);
    }
}