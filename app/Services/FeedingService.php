<?php

namespace App\Services;

use App\Models\Feeding;
use App\Repositories\Interfaces\FeedingRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

class FeedingService
{
    protected $feedingRepository;

    public function __construct(FeedingRepositoryInterface $feedingRepository)
    {
        $this->feedingRepository = $feedingRepository;
    }

    public function getAllFeedings(): Collection
    {
        return $this->feedingRepository->getAll();
    }

    public function getUserFeedings(int $userId): Collection
    {
        return $this->feedingRepository->getByUserId($userId);
    }

    public function getFeedingsByCat(int $catId): Collection
    {
        return $this->feedingRepository->getByCatId($catId);
    }

    public function getTodayFeedings(int $userId): Collection
    {
        return $this->feedingRepository->getTodayFeedings($userId);
    }

    public function getFeedingsByDate(int $catId, string $date): Collection
    {
        $this->validateDate($date);
        return $this->feedingRepository->getFeedingsByDate($catId, $date);
    }

    public function findFeeding(int $id): ?Feeding
    {
        return $this->feedingRepository->findById($id);
    }

    public function createFeeding(array $data): Feeding
    {
        $this->validateFeedingData($data);

        // Преобразуем дату и время
        if (isset($data['date_time'])) {
            $data['date_time'] = Carbon::parse($data['date_time']);
        }

        return $this->feedingRepository->create($data);
    }

    public function updateFeeding(Feeding $feeding, array $data): bool
    {
        $this->validateFeedingData($data, true);

        if (isset($data['date_time'])) {
            $data['date_time'] = Carbon::parse($data['date_time']);
        }

        return $this->feedingRepository->update($feeding, $data);
    }

    public function deleteFeeding(Feeding $feeding): bool
    {
        return $this->feedingRepository->delete($feeding);
    }

    public function getDailyStats(int $catId, string $date): array
    {
        $this->validateDate($date);

        $feedings = $this->feedingRepository->getFeedingsByDate($catId, $date);

        $totalWeight = $feedings->sum('weight_grams');
        $feedingCount = $feedings->count();
        $foodTypes = $feedings->groupBy('food_type')->map->count();

        return [
            'date' => $date,
            'total_feedings' => $feedingCount,
            'total_weight_grams' => $totalWeight,
            'total_weight_kilograms' => round($totalWeight / 1000, 3),
            'avg_weight_per_feeding' => $feedingCount > 0 ? round($totalWeight / $feedingCount) : 0,
            'food_types' => $foodTypes,
            'feedings' => $feedings
        ];
    }

    public function getWeeklyStats(int $catId, string $startDate): array
    {
        $this->validateDate($startDate);

        $start = Carbon::parse($startDate);
        $end = $start->copy()->addDays(6);

        $stats = [];
        $currentDate = $start->copy();

        while ($currentDate->lte($end)) {
            $dateStr = $currentDate->format('Y-m-d');
            $stats[$dateStr] = $this->getDailyStats($catId, $dateStr);
            $currentDate->addDay();
        }

        return [
            'period' => $start->format('Y-m-d') . ' to ' . $end->format('Y-m-d'),
            'total_feedings' => collect($stats)->sum('total_feedings'),
            'total_weight_grams' => collect($stats)->sum('total_weight_grams'),
            'daily_stats' => $stats
        ];
    }

    protected function validateFeedingData(array $data, bool $isUpdate = false): void
    {
        if (isset($data['date_time'])) {
            $feedingTime = Carbon::parse($data['date_time']);

            if ($feedingTime->isFuture() && !$isUpdate) {
                throw new InvalidArgumentException('Время кормления не может быть в будущем');
            }
        }

        if (isset($data['weight_grams']) && $data['weight_grams'] <= 0) {
            throw new InvalidArgumentException('Вес корма должен быть положительным числом');
        }
    }

    protected function validateDate(string $date): void
    {
        if (!strtotime($date)) {
            throw new InvalidArgumentException('Неверный формат даты');
        }

        $dateObj = Carbon::parse($date);
        if ($dateObj->isFuture()) {
            throw new InvalidArgumentException('Дата не может быть в будущем');
        }
    }

    public function getLastFeedingTime(int $catId): ?Carbon
    {
        $lastFeeding = Feeding::where('cat_id', $catId)
            ->orderBy('date_time', 'desc')
            ->first();

        return $lastFeeding?->date_time;
    }

    public function getFeedingFrequency(int $catId, int $days = 7): float
    {
        $startDate = now()->subDays($days);
        $feedings = Feeding::where('cat_id', $catId)
            ->where('date_time', '>=', $startDate)
            ->count();

        return $feedings > 0 ? round($feedings / $days, 2) : 0;
    }
}
