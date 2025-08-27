<?php

namespace App\Services;

use App\Models\HealthRecord;
use App\Repositories\Interfaces\HealthRecordRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

class HealthRecordService
{
    public function __construct(HealthRecordRepositoryInterface $healthRecordRepository)
    {
        $this->healthRecordRepository = $healthRecordRepository;
    }

    /**
     * Получить все записи о здоровье
     */
    public function getAllRecords(): Collection
    {
        return $this->healthRecordRepository->getAll();
    }

    /**
     * Получить записи о здоровье конкретного пользователя
     */
    public function getUserRecords(int $userId): Collection
    {
        return $this->healthRecordRepository->getByUserId($userId);
    }

    /**
     * Получить записи о здоровье конкретного кота
     */
    public function getRecordsByCat(int $catId): Collection
    {
        return $this->healthRecordRepository->getByCatId($catId);
    }

    /**
     * Получить последние записи о здоровье кота
     */
    public function getLatestRecordsByCat(int $catId, int $limit = 10): Collection
    {
        if ($limit <= 0) {
            throw new InvalidArgumentException('Limit must be greater than 0');
        }

        return $this->healthRecordRepository->getLatestByCatId($catId, $limit);
    }

    /**
     * Найти запись о здоровье по ID
     */
    public function findById(int $id): ?HealthRecord
    {
        return $this->healthRecordRepository->findById($id);
    }

    /**
     * Создать новую запись о здоровье
     */
    public function createRecord(array $data): HealthRecord
    {
        // Валидация данных (основная валидация уже в Form Request)
        $this->validateRecordData($data);

        return $this->healthRecordRepository->create($data);
    }

    /**
     * Обновить запись о здоровье
     */
    public function updateRecord(HealthRecord $healthRecord, array $data): bool
    {
        $this->validateRecordData($data, true);

        return $this->healthRecordRepository->update($healthRecord, $data);
    }

    /**
     * Удалить запись о здоровье
     */
    public function deleteRecord(HealthRecord $healthRecord): bool
    {
        return $this->healthRecordRepository->delete($healthRecord);
    }

    /**
     *  Получить статистику веса кота
     */
    public function getWeightStats(int $catId): array
    {
        $records = $this->healthRecordRepository->getByCatId($catId);

        if ($records->isEmpty()) {
            return [
                'min' => null,
                'max' => null,
                'avg' => null,
                'last' => null,
                'count' => 0,
            ];
        }

        $weights = $records->pluck('weight')->filter()->toArray();

        return [
            'min' => min($weights),
            'max' => max($weights),
            'avg' => round(array_sum($weights) / count($weights), 2),
            'last' => $records->first()->weight,
            'count' => count($weights),
            'trend' => $this->calculateWeightTrends($records),
        ];
    }

    /**
     * Валидация записи о данных
     */
    protected function validateRecordData(array $data, bool $isUpdate = false): void
    {
        // Дополнительная бизнес-логика валидации
        if (isset($data['weight']) && $data['weight'] <= 0) {
            throw new InvalidArgumentException('Вес должен быть положительным числом');
        }

        if (isset($data['temperature'])) {
            $temp = (float)$data['temperature'];
            if ($temp < 35 || $temp > 42) {
                throw new InvalidArgumentException('Температура должна быть в диапазоне 35-42 С');
            }
        }

        // Можно добавить больше бизнес-правил по необходимости
    }

    /**
     * Расчет тренда веса (увеличение/уменьшение/стабиольный)
     */
    private function calculateWeightTrends(Collection $records): string
    {
        if ($records->count() < 2) {
            return 'stable';
        }

        $latestRecords = $records->take(3);
        $weights = $latestRecords->pluck('weight')->toArray();

        // Простой анализ тренда по последним 3 записям
        $differences = [];
        for ($i = 1; $i < count($weights); $i++) {
            $differences[] = $weights[$i] - $weights[$i - 1];
        }

        $avgDifference = array_sum($differences) / count($differences);

        if ($avgDifference > 0.1) {
            return 'increasing';
        } elseif ($avgDifference < -0.1) {
            return 'decreasing';
        } else {
            return 'stable';
        }
    }

    /**
     * Проверить, если запись о здоровье для кота на указанную дату
     */
    public function hasRecordForDate(int $catId, string $date): bool
    {
        return HealthRecord::where('cat_id', $catId)
            ->where('record_date', $date)
            ->exists();
    }

    /**
     * Получить последнюю запись о весе кота
     */
    public function getLastWeightRecord(int $catId):?HealthRecord
    {
        return HealthRecord::where('cat_id', $catId)
            ->whereNotNull('weight')
            ->orderBy('record_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
