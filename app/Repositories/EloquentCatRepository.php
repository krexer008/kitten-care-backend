<?php

namespace App\Repositories;

use App\Models\Cat;
use App\Repositories\Interfaces\CatRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentCatRepository implements CatRepositoryInterface
{
    public function getAll(): Collection
    {
        return Cat::all();
    }

    public function findById(int $id): ?Cat
    {
        return Cat::find($id);
    }

    public function create(array $attributes): Cat
    {
        return Cat::create($attributes);
    }

    public function update(Cat $cat, array $attributes): bool
    {
        return $cat->update($attributes);
    }

    public function delete(Cat $cat): bool
    {
        return $cat->delete();
    }

    public function getByUserId(int $userId): ?Collection
    {
        // Важнейший метод! Всегда проверяем, что пользователь запрашивает своих котов
        return Cat::where('user_id', $userId)->get();
    }
}
