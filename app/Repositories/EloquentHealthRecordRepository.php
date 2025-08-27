<?php

namespace App\Repositories;

use App\Models\HealthRecord;
use App\Repositories\Interfaces\HealthRecordRepositoryInterface;
use Dotenv\Repository\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentHealthRecordRepository implements HealthRecordRepositoryInterface
{
    public function getAll(): Collection
    {
        return HealthRecord::all();
    }

    public function findById(int $id): ?HealthRecord
    {
        return HealthRecord::find($id);
    }

    public function create(array $attributes): HealthRecord
    {
        return HealthRecord::create($attributes);
    }

    public function update(HealthRecord $healthRecord, array $attributes): bool
    {
        return $healthRecord->update($attributes);
    }

    public function delete(HealthRecord $healthRecord): bool
    {
        return $healthRecord->delete();
    }

    public function getByUserId(int $userId): Collection
    {
        return HealthRecord::where('user_id', $userId)
            ->with('cat')
            ->orderBy('record_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByCatId(int $catId): Collection
    {
        return HealthRecord::where('cat_id', $catId)
            ->orderBy('record_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getLatestByCatId(int $catId, int $limit = 10): Collection
    {
        return HealthRecord::where('cat_id', $catId)
            ->orderBy('record_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
