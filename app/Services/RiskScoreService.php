<?php

namespace App\Services;

use App\Models\BorrowTransaction;
use App\Models\ReturnTransaction;
use App\Models\RiskRule;
use App\Models\RiskScore;
use App\Models\Project;
use App\Services\AiSuggestionService;

class RiskScoreService
{
    public function __construct(
        private AiSuggestionService $aiSuggestionService,
    ) {}

    /**
     * Evaluate risk after a return transaction.
     */
    public function evaluateReturn(ReturnTransaction $return, BorrowTransaction $borrow): void
    {
        $pic = $borrow->requester;
        $project = $borrow->project;

        // Late return
        if ($return->is_late) {
            $this->addScore($pic->id, $project->id, $borrow->id, $return->id, 'late_return',
                "Late by {$return->days_late} days on borrow {$borrow->code}");
        }

        // Quantity mismatch (lost items)
        if ($return->quantity_lost > 0) {
            $this->addScore($pic->id, $project->id, $borrow->id, $return->id, 'quantity_mismatch',
                "{$return->quantity_lost} item(s) lost on return {$return->code}");
        }

        // Damage
        if ($return->quantity_damaged > 0) {
            $this->addScore($pic->id, $project->id, $borrow->id, $return->id, 'damage',
                "{$return->quantity_damaged} item(s) damaged on return {$return->code}");
        }

        // Update project risk level
        $this->recalculateProjectRisk($project);

        // Run AI analysis
        $this->aiSuggestionService->analyzeAndGenerate($pic, $project);
    }

    public function addScore(
        int $userId,
        ?int $projectId,
        ?int $borrowId,
        ?int $returnId,
        string $ruleCode,
        string $notes = null
    ): void {
        $rule = RiskRule::where('code', $ruleCode)->where('is_active', true)->first();
        if (!$rule) return;

        RiskScore::create([
            'user_id'                => $userId,
            'project_id'             => $projectId,
            'borrow_transaction_id'  => $borrowId,
            'return_transaction_id'  => $returnId,
            'risk_rule_id'           => $rule->id,
            'points_added'           => $rule->points,
            'notes'                  => $notes,
        ]);
    }

    public function recalculateProjectRisk(Project $project): void
    {
        $totalScore = $project->riskScores()->sum('points_added');
        $level = $this->scoreToLevel($totalScore);

        $project->update([
            'risk_score' => $totalScore,
            'risk_level' => $level,
        ]);
    }

    public function getUserTotalScore(int $userId): int
    {
        return RiskScore::where('user_id', $userId)->sum('points_added');
    }

    public function getTopRiskUsers(int $limit = 10): \Illuminate\Support\Collection
    {
        return RiskScore::selectRaw('user_id, SUM(points_added) as total_score')
            ->groupBy('user_id')
            ->orderByDesc('total_score')
            ->limit($limit)
            ->with('user')
            ->get();
    }

    public function getTopRiskProjects(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Project::orderByDesc('risk_score')
            ->where('risk_score', '>', 0)
            ->limit($limit)
            ->with('pic')
            ->get();
    }

    private function scoreToLevel(int $score): string
    {
        if ($score >= 20) return 'critical';
        if ($score >= 10) return 'high';
        if ($score >= 5)  return 'medium';
        return 'low';
    }
}
