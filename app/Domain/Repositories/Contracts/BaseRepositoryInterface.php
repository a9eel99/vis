<?php

namespace App\Domain\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BaseRepositoryInterface
{
    public function all(array $columns = ['*']): Collection;

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    public function find(string $id, array $relations = []): ?Model;

    public function findOrFail(string $id, array $relations = []): Model;

    public function create(array $data): Model;

    public function update(string $id, array $data): Model;

    public function delete(string $id): bool;

    public function count(): int;
}
