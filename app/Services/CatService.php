<?php

namespace App\Services;

use App\Models\Cat;
use App\Repositories\Interfaces\CatRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CatService
{
    protected $catRepository;

    public function __construct(CatRepositoryInterface $catRepository)
    {
        $this->catRepository = $catRepository;
    }

    public function getAllCats(): Collection
    {
        return $this->catRepository->getAll();
    }

    public function getUsersCats(int $userId): Collection
    {
        // Здесь может быть доп. логика, например, проверка прав пользователя
        return $this->catRepository->getByUserId($userId);
    }

    public function createCat(array $data, int $userId): Cat
    {
        // Валидация данных могла бы быть здесь, но лучше в Form Request
        // Объединяем массив данных с ID пользователя
        $dataToCreate = array_merge($data, ['user_id' => $userId]);
        return $this->catRepository->create($dataToCreate);
    }

    public function updateCat(Cat $cat, array $data): bool
    {
        // Проверка, что пользователь может редактировать этого кота
        // if (auth()->id() !== $cat->user_id) { ... }
        return $this->catRepository->update($cat, $data);
    }

    public function deleteCat(Cat $cat): bool
    {
        // Проверка прав
        return $this->catRepository->delete($cat);
    }
}
