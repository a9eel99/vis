<?php

namespace App\Domain\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface VehicleRepositoryInterface extends BaseRepositoryInterface
{
    public function search(string $query, int $perPage = 15): LengthAwarePaginator;

    public function getWithInspections(string $id);

    public function getByOwner(string $ownerEmail): \Illuminate\Support\Collection;
}
