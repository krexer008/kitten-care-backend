<?php

namespace App\Repositories;

use App\Models\VeterinaryVisit;
use App\Repositories\Interfaces\VeterinaryVisitRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentVeterinaryVisitRepository implements VeterinaryVisitRepositoryInterface
{

    public function getAll(): Collection
    {
        return VeterinaryVisit::all();
    }

    public function findById(int $id): ?VeterinaryVisit
    {
        return VeterinaryVisit::find($id);
    }

    public function create(array $attributes): VeterinaryVisit
    {
        return VeterinaryVisit::create($attributes);
    }

    public function update(VeterinaryVisit $veterinaryVisit, array $attributes): bool
    {
        return $veterinaryVisit->update($attributes);
    }

    public function delete(VeterinaryVisit $veterinaryVisit): bool
    {
        return $veterinaryVisit->delete();
    }

    public function getByUserId(int $userId): Collection
    {
        return VeterinaryVisit::where('user_id', $userId)
            ->with('cat')
            ->orderBy('date_time', 'desc')
            ->get();
    }

    public function getByCatId(int $catId): Collection
    {
        return VeterinaryVisit::where('cat_id', $catId)
            ->orderBy('date_time', 'desc')
            ->get();
    }

    public function getUpcomingVisits(int $userId, int $days = 30): Collection
    {
        return VeterinaryVisit::where('user_id', $userId)
            ->where('date_time', '>', now())
            ->where('date_time', '<=', now()->addDays($days))
            ->with('cat')
            ->orderBy('date_time', 'asc')
            ->get();
    }

    public function getPastVisits(int $userId, int $limit = 10): Collection
    {
        return VeterinaryVisit::where('user_id', $userId)
            ->where('date_time', '<=', now())
            ->with('cat')
            ->orderBy('date_time', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getVisitsByDateRange(int $userId, string $startDate, string $endDate): Collection
    {
        return VeterinaryVisit::where('user_id', $userId)
            ->whereBetween('date_time', [$startDate, $endDate])
            ->with('cat')
            ->orderBy('date_time', 'asc')
            ->get();
    }
}
