<?php

namespace App\Providers;

use App\Models\Workload;
use App\Observers\WorkloadObserver;
use App\Services\{
    WorkloadService,
    WorkloadDataService,
    WorkloadValidationService,
    WorkloadStatisticsService,
    WorkloadReportService
};
use Illuminate\Support\ServiceProvider;

class WorkloadServiceProvider extends ServiceProvider
{
    public array $singletons = [
        WorkloadService::class => WorkloadService::class,
        WorkloadDataService::class => WorkloadDataService::class,
        WorkloadValidationService::class => WorkloadValidationService::class,
        WorkloadStatisticsService::class => WorkloadStatisticsService::class,
        WorkloadReportService::class => WorkloadReportService::class,
    ];

    public function boot(): void
    {
        Workload::observe(WorkloadObserver::class);
    }
}
