<?php

namespace App\Providers;

use App\Models\Cat;
use App\Models\Feeding;
use App\Models\HealthRecord;
use App\Models\VeterinaryVisit;
use App\Policies\CatPolicy;
use App\Policies\FeedingPolicy;
use App\Policies\HealthRecordPolicy;
use App\Policies\VeterinaryVisitPolicy;
use App\Repositories\EloquentCatRepository;
use App\Repositories\EloquentFeedingRepository;
use App\Repositories\EloquentHealthRecordRepository;
use App\Repositories\EloquentVeterinaryVisitRepository;
use App\Repositories\Interfaces\CatRepositoryInterface;
use App\Repositories\Interfaces\FeedingRepositoryInterface;
use App\Repositories\Interfaces\HealthRecordRepositoryInterface;
use App\Repositories\Interfaces\VeterinaryVisitRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    protected $policies = [
        Cat::class => CatPolicy::class,
        Feeding::class => FeedingPolicy::class,
        HealthRecord::class => HealthRecordPolicy::class,
        VeterinaryVisit::class => VeterinaryVisitPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CatRepositoryInterface::class, EloquentCatRepository::class);
        $this->app->bind(FeedingRepositoryInterface::class, EloquentFeedingRepository::class);
        $this->app->bind(HealthRecordRepositoryInterface::class, EloquentHealthRecordRepository::class);
        $this->app->bind(VeterinaryVisitRepositoryInterface::class, EloquentVeterinaryVisitRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
