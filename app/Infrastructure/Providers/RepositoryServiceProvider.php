<?php

namespace App\Infrastructure\Providers;

use App\Domain\Repositories\Contracts\InspectionRepositoryInterface;
use App\Domain\Repositories\Contracts\TemplateRepositoryInterface;
use App\Domain\Repositories\Contracts\VehicleRepositoryInterface;
use App\Infrastructure\Repositories\InspectionRepository;
use App\Infrastructure\Repositories\TemplateRepository;
use App\Infrastructure\Repositories\VehicleRepository;
use App\Domain\Models\Inspection;
use App\Domain\Models\Vehicle;
use App\Domain\Models\InspectionTemplate;
use App\Domain\Models\User;
use App\Domain\Models\Customer;
use App\Domain\Models\AuditLog;
use App\Policies\InspectionPolicy;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(VehicleRepositoryInterface::class, VehicleRepository::class);
        $this->app->bind(InspectionRepositoryInterface::class, InspectionRepository::class);
        $this->app->bind(TemplateRepositoryInterface::class, TemplateRepository::class);
        $this->app->singleton(\App\Application\Services\PuppeteerReportService::class);
    }

    public function boot(): void
    {
        // Register policies (auto-discovery won't find them due to non-standard model path)
        Gate::policy(Inspection::class, InspectionPolicy::class);

        // Morph map for audit logs — prevents breakage if namespaces change (BUG-10 fix)
        Relation::enforceMorphMap([
            'inspection' => Inspection::class,
            'vehicle' => Vehicle::class,
            'template' => InspectionTemplate::class,
            'user' => User::class,
            'customer' => Customer::class,
            'audit_log' => AuditLog::class,
        ]);
    }
}