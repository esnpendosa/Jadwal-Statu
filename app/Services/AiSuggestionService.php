<?php

namespace App\Services;

use App\Models\AiSuggestion;
use App\Models\AiSuggestionRule;
use App\Models\BorrowTransaction;
use App\Models\Inventory;
use App\Models\Project;
use App\Models\User;

class AiSuggestionService
{
    /**
     * Analyze a specific PIC/project for risk patterns and generate suggestions.
     */
    public function analyzeAndGenerate(User $pic, Project $project): void
    {
        $rules = AiSuggestionRule::active()->get();
        $now   = now();

        foreach ($rules as $rule) {
            $periodStart = $now->copy()->subDays((int) $rule->period_days);
            $count = 0;

            switch ($rule->trigger_type) {
                case 'late_return_frequency':
                    $count = \App\Models\ReturnTransaction::where('is_late', true)
                        ->where('returned_by', $pic->id)
                        ->where('created_at', '>=', $periodStart)
                        ->count();
                    break;

                case 'damage_frequency':
                    $count = \App\Models\ReturnTransaction::where('quantity_damaged', '>', 0)
                        ->where('returned_by', $pic->id)
                        ->where('created_at', '>=', $periodStart)
                        ->count();
                    break;

                case 'mismatch_frequency':
                    $count = \App\Models\ReturnTransaction::where('quantity_lost', '>', 0)
                        ->where('returned_by', $pic->id)
                        ->where('created_at', '>=', $periodStart)
                        ->count();
                    break;

                case 'high_risk_score':
                    $count = \App\Models\RiskScore::where('user_id', $pic->id)
                        ->where('created_at', '>=', $periodStart)
                        ->sum('points_added');
                    break;
            }

            if ($count >= $rule->threshold) {
                $this->createSuggestion($rule, $pic, $project);
            }
        }
    }

    /**
     * Run global analysis for all active entities (Users, Projects, Inventories).
     */
    public function runGlobalAnalysis(): void
    {
        $rules = AiSuggestionRule::active()->get();
        if ($rules->isEmpty()) return;

        // 1. Analyze PIC Patterns
        $pics = User::role('PIC')->get();
        foreach ($pics as $pic) {
            $projects = $pic->projectsAsPic()->active()->get();
            foreach ($projects as $project) {
                $this->analyzeAndGenerate($pic, $project);
            }
        }

        // 2. Analyze Inventory Patterns (Mismatches/Damage)
        $this->analyzeInventoryPatterns();

        // 3. Analyze Stock Risks (Low stock or supply chain issues)
        $this->analyzeStockRisks();
    }

    /**
     * Analyze which inventory items are frequently mismatched or damaged.
     */
    public function analyzeInventoryPatterns(): void
    {
        // Items with frequent lost/damaged history
        $inventories = Inventory::where('lost_count', '>', 5)
            ->orWhere('damaged_count', '>', 10)
            ->get();

        foreach ($inventories as $inventory) {
            $trigger = $inventory->lost_count > 5 ? 'mismatch_frequency' : 'damage_frequency';
            $rule = AiSuggestionRule::where('trigger_type', $trigger)
                ->where('target', 'inventory')
                ->active()->first();

            if ($rule) {
                $this->createSuggestionForInventory($rule, $inventory);
            }
        }
    }

    /**
     * Analyze items at high risk of depletion.
     */
    public function analyzeStockRisks(): void
    {
        $lowStockItems = Inventory::active()->lowStock()->get();
        
        foreach ($lowStockItems as $item) {
            // Check if there are active projects using this item
            $activeConsumption = \App\Models\BorrowItem::where('inventory_id', $item->id)
                ->whereHas('borrowTransaction', fn($q) => $q->where('status', 'borrowed'))
                ->sum('quantity');

            if ($activeConsumption > 0 || $item->stock_available == 0) {
                $rule = AiSuggestionRule::where('trigger_type', 'high_risk_score') // Generic trigger for critical stock
                    ->where('target', 'inventory')
                    ->active()->first();
                
                if ($rule) {
                    $this->createSuggestionForInventory($rule, $item);
                }
            }
        }
    }

    public function analyzeSyncInventory(Inventory $inventory): void
    {
        // Immediate check for a single item after stock change
        if ($inventory->getIsLowStockAttribute()) {
            $this->analyzeStockRisks();
        }
        if ($inventory->lost_count > 5 || $inventory->damaged_count > 5) {
            $this->analyzeInventoryPatterns();
        }
    }

    private function createSuggestion(AiSuggestionRule $rule, User $pic, Project $project): void
    {
        // Avoid duplicate within 12 hours for real-time feel
        $exists = AiSuggestion::where('ai_suggestion_rule_id', $rule->id)
            ->where('target_type', 'user')
            ->where('target_id', $pic->id)
            ->where('generated_at', '>=', now()->subHours(12))
            ->exists();

        if ($exists) return;

        AiSuggestion::create([
            'ai_suggestion_rule_id' => $rule->id,
            'target_type'           => 'user',
            'target_id'             => $pic->id,
            'target_label'          => $pic->name,
            'suggestion_text'       => $rule->suggestion,
            'severity'              => $rule->severity,
            'generated_at'          => now(),
        ]);
    }

    private function createSuggestionForInventory(AiSuggestionRule $rule, Inventory $inventory): void
    {
        $exists = AiSuggestion::where('ai_suggestion_rule_id', $rule->id)
            ->where('target_type', 'inventory')
            ->where('target_id', $inventory->id)
            ->where('generated_at', '>=', now()->subHours(12))
            ->exists();

        if ($exists) return;

        AiSuggestion::create([
            'ai_suggestion_rule_id' => $rule->id,
            'target_type'           => 'inventory',
            'target_id'             => $inventory->id,
            'target_label'          => $inventory->name,
            'suggestion_text'       => $rule->suggestion,
            'severity'              => $rule->severity,
            'generated_at'          => now(),
        ]);
    }

    public function getDashboardInsights(): array
    {
        return [
            'high_risk_projects' => \App\Models\Project::orderByDesc('risk_score')
                ->where('risk_score', '>', 0)
                ->limit(5)->with('pic')->get(),
            'top_risk_pics'      => \App\Models\RiskScore::selectRaw('user_id, SUM(points_added) as total')
                ->groupBy('user_id')->orderByDesc('total')->limit(5)->with('user')->get(),
            'frequent_mismatch'  => Inventory::where('lost_count', '>', 0)
                ->orderByDesc('lost_count')->limit(5)->with('category')->get(),
            'active_suggestions' => AiSuggestion::active()->orderByDesc('generated_at')->limit(6)->get(),
        ];
    }
}
