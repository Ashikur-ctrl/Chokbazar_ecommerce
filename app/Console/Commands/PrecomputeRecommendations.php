<?php

namespace App\Console\Commands;

use App\Services\RecommendationService;
use Illuminate\Console\Command;

class PrecomputeRecommendations extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'recommendations:precompute {--users=100 : Number of active users to process}';

    /**
     * The console command description.
     */
    protected $description = 'Precompute product recommendations for active users';

    protected RecommendationService $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        parent::__construct();
        $this->recommendationService = $recommendationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting recommendation precomputation...');

        $userLimit = (int) $this->option('users');

        $bar = $this->output->createProgressBar($userLimit);
        $bar->start();

        // Precompute recommendations for active users
        $this->recommendationService->precomputeRecommendations();

        $bar->finish();
        $this->newLine();

        $this->info('Recommendation precomputation completed!');
        $this->info('Cached recommendations for active users and popular products.');
    }
}
