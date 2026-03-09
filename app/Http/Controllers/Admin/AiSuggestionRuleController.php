<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiSuggestionRule;
use Illuminate\Http\Request;

class AiSuggestionRuleController extends Controller
{
    public function index()
    {
        $rules = AiSuggestionRule::orderBy('sort_order')->get();
        return view('admin.ai-rules.index', compact('rules'));
    }

    public function create()
    {
        return view('admin.ai-rules.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'trigger_type'   => 'required|in:late_return_frequency,damage_frequency,mismatch_frequency,high_risk_score',
            'threshold'      => 'required|integer|min:1',
            'period_days'    => 'required|integer|min:1',
            'target'         => 'required|in:pic,project,inventory,location',
            'severity'       => 'required|in:info,warning,critical',
            'sort_order'     => 'required|integer|min:0',
            'suggestion.id'  => 'required|string|max:1000',
            'suggestion.en'  => 'required|string|max:1000',
            'suggestion.zh'  => 'nullable|string|max:1000',
        ]);

        $data['suggestion'] = [
            'id' => $request->input('suggestion.id'),
            'en' => $request->input('suggestion.en'),
            'zh' => $request->input('suggestion.zh') ?? '',
        ];

        AiSuggestionRule::create($data);
        return redirect()->route('admin.ai-rules.index')->with('success', __('admin.ai_rule_created'));
    }

    public function edit(AiSuggestionRule $aiRule)
    {
        return view('admin.ai-rules.edit', ['rule' => $aiRule]);
    }

    public function update(Request $request, AiSuggestionRule $aiRule)
    {
        $data = $request->validate([
            'threshold'      => 'required|integer|min:1',
            'period_days'    => 'required|integer|min:1',
            'severity'       => 'required|in:info,warning,critical',
            'sort_order'     => 'required|integer|min:0',
            'suggestion.id'  => 'required|string|max:1000',
            'suggestion.en'  => 'required|string|max:1000',
            'suggestion.zh'  => 'nullable|string|max:1000',
        ]);

        $data['suggestion'] = [
            'id' => $request->input('suggestion.id'),
            'en' => $request->input('suggestion.en'),
            'zh' => $request->input('suggestion.zh') ?? '',
        ];

        $aiRule->update($data);
        return redirect()->route('admin.ai-rules.index')->with('success', __('admin.ai_rule_updated'));
    }

    public function destroy(AiSuggestionRule $aiRule)
    {
        $aiRule->delete();
        return redirect()->route('admin.ai-rules.index')->with('success', __('admin.ai_rule_deleted'));
    }

    public function toggle(AiSuggestionRule $aiRule)
    {
        $aiRule->update(['is_active' => !$aiRule->is_active]);
        return back()->with('success', __('admin.status_updated'));
    }
}
