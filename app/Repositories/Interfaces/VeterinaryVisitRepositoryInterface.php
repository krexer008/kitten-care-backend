<?php

namespace App\Repositories\Interfaces;

use App\Models\VeterinaryVisit;
use Illuminate\Database\Eloquent\Collection;

interface VeterinaryVisitRepositoryInterface
{
    public function getAll(): Collection;

    public function findById(int $id): ?VeterinaryVisit;

    public function create(array $attributes): VeterinaryVisit;

    public function update(VeterinaryVisit $veterinaryVisit, array $attributes): bool;

    public function delete(VeterinaryVisit $veterinaryVisit): bool;

    public function getByUserId(int $userId): Collection;

    public function getByCatId(int $catId): Collection;

    public function getUpcomingVisits(int $userId, int $days = 30): Collection;

    public function getPastVisits(int $userId, int $limit = 10): Collection;

    public function getVisitsByDateRange(int $userId, string $startDate, string $endDate): Collection;
}
