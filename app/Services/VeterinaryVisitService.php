<?php

namespace App\Services;


use App\Models\VeterinaryVisit;
use App\Repositories\Interfaces\VeterinaryVisitRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

class VeterinaryVisitService
{
    protected VeterinaryVisitRepositoryInterface $veterinaryVisitRepository;

    public function __construct(VeterinaryVisitRepositoryInterface $veterinaryVisitRepository)
    {
        $this->veterinaryVisitRepository = $veterinaryVisitRepository;
    }

    public function getAllVisits(): Collection
    {
        return $this->veterinaryVisitRepository->getAll();
    }

    public function getUserVisits(int $userId): Collection
    {
        return $this->veterinaryVisitRepository->getByUserId($userId);
    }

    public function getVisitsByCat(int $catId): Collection
    {
        return $this->veterinaryVisitRepository->getByCatId($catId);
    }

    /**
     * Получить предстоящие визиты пользователя
     */
    public function getUpcomingVisits(int $userId, int $days = 30): Collection
    {
        if ($days <= 0) {
            throw new InvalidArgumentException('Количество дней должно быть положительным числом');
        }

        return $this->veterinaryVisitRepository->getUpcomingVisits($userId, $days);
    }

    /**
     * Получить прошедшие визиты пользователя
     */
    public function getPastVisits(int $userId, int $limit = 10): Collection
    {
        if ($limit <= 0) {
            throw new InvalidArgumentException('Лимит должен быть положительным числом');
        }

        return $this->veterinaryVisitRepository->getPastVisits($userId, $limit);
    }

    /**
     * Получить визиты за период времени
     */
    public function getVisitsByDateRange(int $userId, string $startDate, string $endDate): Collection
    {
        $this->validateDateRange($startDate, $endDate);

        return $this->veterinaryVisitRepository->getVisitsByDateRange($userId, $startDate, $endDate);
    }

    /**
     * Найти визит по ID
     */
    public function findVisit(int $id): ?VeterinaryVisit
    {
        return $this->veterinaryVisitRepository->findById($id);
    }

    /**
     * Создать новый визит
     */
    public function createVisit(array $data): VeterinaryVisit
    {
        $this->validateVisitData($data);

        // Преобразуем дату и время в правильный формат
        if (isset($data['date_time'])) {
            $data['date_time'] = Carbon::parse($data['date_time']);
        }

        if (isset($data['next_visit_time'])) {
            $data['next_visit_time'] = Carbon::parse($data['next_visit_time']);
        }

        return $this->veterinaryVisitRepository->create($data);
    }

    /**
     * Обновить ветеринарный визит
     */
    public function updateVisit(VeterinaryVisit $veterinaryVisit, array $data): bool
    {
        $this->validateVisitData($data, true);

        // Преобразуем дату и время при обновлении
        if (isset($data['date_time'])) {
            $data['date_time'] = Carbon::parse($data['date_time']);
        }

        if (isset($data['next_visit_time'])) {
            $data['next_visit_time'] = Carbon::parse($data['next_visit_time']);
        }

        return $this->veterinaryVisitRepository->update($veterinaryVisit, $data);
    }

    public function deleteVisit(VeterinaryVisit $veterinaryVisit): bool
    {
        return $this->veterinaryVisitRepository->delete($veterinaryVisit);
    }

    /**
     * Получить статистику по визитам
     */
    public function getVisitStats(int $userId): array
    {
        $allVisits = $this->veterinaryVisitRepository->getByUserId($userId);
        $upcomingVisits = $this->veterinaryVisitRepository->getUpcomingVisits($userId);
        $pastVisits = $this->veterinaryVisitRepository->getPastVisits($userId, 1000);

        $clinics = $allVisits->groupBy('clinic_name')->map->count();
        $reasons = $allVisits->groupBy('reason')->map->count();

        return [
            'total_visits' => $allVisits->count(),
            'upcoming_visits' => $upcomingVisits->count(),
            'past_visits' => $pastVisits->count(),
            'favorite_clinics' => $clinics->sortDesc()->take(5),
            'common_reasons' => $reasons->sortDesc()->take(5),
            'last_visit' => $pastVisits->first()?->date_time?->format('d.m.Y'),
            'next_visit' => $upcomingVisits->first()?->date_time?->format('d.m.Y'),
        ];
    }

    /**
     * Получить визиты требующие напоминания (за N дней до визита)
     */
    public function getVisitsNeedingReminder(int $daysBefore = 1): Collection
    {
        $reminderDate = now()->addDays($daysBefore);

        return VeterinaryVisit::where('date_time', '>=', now())
            ->where('date_time', '<=', $reminderDate)
            ->with(['cat', 'user'])
            ->get();
    }

    /**
     * Проверить есть ли конфликт по времени визитов
     */
    public function hasTimeConflict(int $userId, string $dateTime, int $excludeVisitId = null): bool
    {
        $visitTime = Carbon::parse($dateTime);
        $startTime = $visitTime->copy()->subHours(2);
        $endTime = $visitTime->copy()->addHours(2);

        $query = VeterinaryVisit::where('user_id', $userId)
            ->whereBetween('date_time', [$startTime, $endTime]);

        if ($excludeVisitId) {
            $query->where('id', '!=', $excludeVisitId);
        }

        return $query->exists();
    }

    /**
     * Валидация данных
     */
    protected function validateVisitData(array $data, bool $isUpdate = false): void
    {
        if (isset($data['date_time'])) {
            $visitTime = Carbon::parse($data['date_time']);

            if ($visitTime->isPast() && !$isUpdate) {
                throw new InvalidArgumentException('Дата и время визита не могут быть в прошлом');
            }
        }

        // Проверка на конфликт времени
        if (isset($data['date_time']) && isset($data['user_id'])) {
            $excludeId = $isUpdate && isset($data['id']) ? $data['id'] : null;

            if ($this->hasTimeConflict($data['user_id'], $data['date_time'], $excludeId)) {
                throw new InvalidArgumentException('На это время уже запланирован другой визит');
            }
        }
    }

    /**
     * Валидация диапазона дат
     */
    protected function validateDateRange(string $startDate, string $endDate): void
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        if ($start->gt($end)) {
            throw new InvalidArgumentException('Начальная дата не может быть больше конечной');
        }

        if ($start->diffInDays($end) > 365) {
            throw new InvalidArgumentException('Диапазон дат не может превышать 1 год');
        }
    }

    /**
     * Получить ближайший визит для кота
     */
    public function getNextVisitForCat(int $catId): ?VeterinaryVisit
    {
        return VeterinaryVisit::where('cat_id', $catId)
            ->where('date_time', '>', now())
            ->orderBy('date_time', 'asc')
            ->first();
    }

    /**
     * Получить историю визитов для кота
     */
    public function getVisitHistoryForCat(int $catId, int $limit = 10): Collection
    {
        return VeterinaryVisit::where('cat_id', $catId)
            ->where('date_time', '<=', now())
            ->orderBy('date_time', 'desc')
            ->limit($limit)
            ->get();
    }
}
