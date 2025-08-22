<?php

namespace App\Repositories\Interfaces;

use App\Models\Cat;
use Illuminate\Database\Eloquent\Collection;

interface CatRepositoryInterface
{
    public function getAll(): Collection;

    public function findById(int $id): ?Cat;

    public function create(array $attributes): Cat;

    public function update(Cat $cat, array $attributes): bool;

    public function delete(Cat $cat): bool;

    public function getByUserId(int $userId): ?Collection;
}
