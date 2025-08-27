<?php

namespace App\Repositories\Interfaces;

use App\Models\Feeding;
use Illuminate\Database\Eloquent\Collection;

interface FeedingRepositoryInterface
{
    public function getAll(): Collection;

    public function findById(int $id): ?Feeding;

    public function create(array $attributes): Feeding;

    public function update(Feeding $feeding, array $attributes): bool;

    public function delete(Feeding $feeding): bool;

    public function getByUserId(int $userId): Collection;

    public function getByCatId(int $catId): Collection;

    public function getTodayFeedings(int $userId): Collection;

    public function getFeedingsByDate(int $catId, string $date): Collection;

    public function getFeedingsStats(int $catId, string $startDate, string $endDate): array;
}
