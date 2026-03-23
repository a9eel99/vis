<?php

namespace App\Domain\Repositories\Contracts;

use Illuminate\Support\Collection;

interface TemplateRepositoryInterface extends BaseRepositoryInterface
{
    public function getActive(): Collection;

    public function getWithSections(string $id);

    public function getWithFullStructure(string $id);

    public function duplicate(string $id);
}
