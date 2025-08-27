<?php

namespace App\Repositories;

use App\Models\Feeding;
use App\Repositories\Interfaces\FeedingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentFeedingRepository implements FeedingRepositoryInterface
{

    public function getAll(): Collection
    {
        return Feeding::all();
    }

    public function findById(int $id): ?Feeding
    {
        return Feeding::find($id);
    }

    public function create(array $attributes): Feeding
    {
        return Feeding::create($attributes);
    }

    public function update(Feeding $feeding, array $attributes): bool
    {
        return $feeding->update($attributes);
    }

    public function delete(Feeding $feeding): bool
    {
        return $feeding->delete();
    }

    public function getByUserId(int $userId): Collection
    {
        return Feeding::where('user_id', $userId)
            ->with('cat')
            ->orderBy('date_time', 'desc')
            ->get();
    }

    public function getByCatId(int $catId): Collection
    {
        return Feeding::where('cat_id', $catId)
            ->orderBy('date_time', 'desc')
            ->get();
    }

    public function getTodayFeedings(int $userId): Collection
    {
        return Feeding::where('user_id', $userId)
            ->where('date_time', today())
            ->with('cat')
            ->orderBy('date_time', 'desc')
            ->get();
    }

    public function getFeedingsByDate(int $catId, string $date): Collection
    {
        return Feeding::where('cat_id', $catId)
            ->where('date_time', $date)
            ->orderBy('date_time', 'asc')
            ->get();
    }

    public function getFeedingsStats(int $catId, string $startDate, string $endDate): array
    {
        return Feeding::where('cat_id', $catId)
            ->whereBetween('date_time', [$startDate, $endDate])
            ->select(
                DB::raw('COUNT(*) as total_feedings'),
                DB::raw('SUM(weight_gram) as total_weight'),
                DB::raw('AVG(weight_grams) as avg_weight'),
                DB::raw('MIN(date_time) as first_feeding'),
                DB::raw('MAX(date_time) as last_feeding'),
            )
            ->first()
            ->toArray();
    }
}
