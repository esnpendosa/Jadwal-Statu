<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RiskRule;
use Illuminate\Http\Request;

class RiskRuleController extends Controller
{
    public function index()
    {
        $rules = RiskRule::latest()->get();
        return view('admin.risk-rules.index', compact('rules'));
    }

    public function create()
    {
        return view('admin.risk-rules.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'        => 'required|string|max:50|unique:risk_rules,code',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'points'      => 'required|integer|min:0',
            'category'    => 'required|in:behavior,damage,loss',
        ]);
        RiskRule::create($data);
        return redirect()->route('admin.risk-rules.index')->with('success', __('admin.risk_rule_created'));
    }

    public function edit(RiskRule $riskRule)
    {
        return view('admin.risk-rules.edit', ['rule' => $riskRule]);
    }

    public function update(Request $request, RiskRule $riskRule)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'points'      => 'required|integer|min:0',
            'category'    => 'required|in:behavior,damage,loss',
        ]);
        $riskRule->update($data);
        return redirect()->route('admin.risk-rules.index')->with('success', __('admin.risk_rule_updated'));
    }

    public function destroy(RiskRule $riskRule)
    {
        $riskRule->delete();
        return redirect()->route('admin.risk-rules.index')->with('success', __('admin.risk_rule_deleted'));
    }

    public function toggle(RiskRule $riskRule)
    {
        $riskRule->update(['is_active' => !$riskRule->is_active]);
        return back()->with('success', __('admin.status_updated'));
    }
}
