<?php

namespace App\Console\Commands;

use App\Services\AiSuggestionService;
use Illuminate\Console\Command;

class RunAiAnalysis extends Command
{
    protected $signature   = 'ai:run-analysis';
    protected $description = 'Run AI suggestion analysis for all active PICs and projects';

    public function handle(AiSuggestionService $service): void
    {
        $this->info('Running AI risk analysis...');
        $service->runGlobalAnalysis();
        $this->info('AI analysis completed.');
    }
}
