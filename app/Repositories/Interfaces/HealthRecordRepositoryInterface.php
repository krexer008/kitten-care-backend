<?php

namespace App\Repositories\Interfaces;

use App\Http\Requests\StoreVeterinaryVisitRequest;
use App\Models\HealthRecord;
use Illuminate\Database\Eloquent\Collection;

interface HealthRecordRepositoryInterface
{
    public function getAll(): Collection;

    public function findById(int $id): ?HealthRecord;

    public function create(array $attributes): HealthRecord;

    public function update(HealthRecord $healthRecord, array $attributes): bool;

    public function delete(HealthRecord $healthRecord): bool;

    public function getByUserId(int $userId): Collection;

    public function getByCatId(int $catId): Collection;

    public function getLatestByCatId(int $catId, int $limit = 10): Collection;
}
