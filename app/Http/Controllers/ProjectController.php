<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function __construct(
        private \App\Services\AiSuggestionService $aiService,
    ) {}

    /**
     * Ambil daftar user PIC (role PIC) untuk dropdown
     */
    private function getPics(): \Illuminate\Database\Eloquent\Collection
    {
        $pics = User::role('PIC')->where('is_active', true)->get();
        if ($pics->isEmpty()) {
            // Fallback: tampilkan semua user aktif
            $pics = User::where('is_active', true)->get();
        }
        return $pics;
    }

    public function index(Request $request)
    {
        $user  = auth()->user();
        $isPic = $user->hasRole('PIC');

        $projects = Project::with(['pic'])
            ->when($isPic, fn($q) => $q->byPic($user->id))
            ->when($request->status,     fn($q) => $q->where('status', $request->status))
            ->when($request->risk_level, fn($q) => $q->where('risk_level', $request->risk_level))
            ->when($request->search,     fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('code', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate(15)->withQueryString();

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $pics = $this->getPics();
        return view('projects.create', compact('pics'));
    }

    public function store(ProjectRequest $request)
    {
        $data = $request->validated();
        $data['code']       = 'PRJ-' . strtoupper(Str::random(6)) . '-' . date('Y');
        $data['created_by'] = auth()->id();
        $data['risk_score'] = 0;
        $data['risk_level'] = 'low';

        // Jika tidak ada pic_id, assign ke creator
        if (empty($data['pic_id'])) {
            $data['pic_id'] = auth()->id();
        }

        $project = Project::create($data);
        AuditLog::log('create', "Created project: {$project->name}", $project);

        $this->aiService->analyzeAndGenerate($project->pic, $project);

        return redirect()->route('projects.show', $project)
            ->with('success', __('projects.created'));
    }

    public function show(Project $project)
    {
        $project->load(['pic', 'creator', 'riskScores.riskRule', 'aiSuggestions']);
        $borrows = $project->borrowTransactions()
            ->with(['items.inventory', 'requester'])
            ->latest()
            ->paginate(10);

        return view('projects.show', compact('project', 'borrows'));
    }

    public function edit(Project $project)
    {
        $pics = $this->getPics();
        return view('projects.edit', compact('project', 'pics'));
    }

    public function update(ProjectRequest $request, Project $project)
    {
        $old  = $project->toArray();
        $data = $request->validated();

        // Jika tidak ada pic_id, pertahankan yang lama
        if (empty($data['pic_id'])) {
            $data['pic_id'] = $project->pic_id;
        }

        $project->update($data);
        AuditLog::log('update', "Updated project: {$project->name}", $project, $old, $data);

        $this->aiService->analyzeAndGenerate($project->pic, $project);

        return redirect()->route('projects.show', $project)
            ->with('success', __('projects.updated'));
    }

    public function destroy(Project $project)
    {
        if ($project->borrowTransactions()->whereIn('status', ['pending', 'approved', 'borrowed'])->exists()) {
            return back()->with('error', __('projects.cannot_delete_active_borrows'));
        }

        AuditLog::log('delete', "Deleted project: {$project->name}", $project);
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', __('projects.deleted'));
    }

    public function complete(Project $project)
    {
        if ($project->borrowTransactions()->whereIn('status', ['pending', 'approved', 'borrowed'])->exists()) {
            return back()->with('error', __('projects.cannot_complete_active_borrows'));
        }

        $project->update(['status' => 'completed']);
        AuditLog::log('complete', "Completed project: {$project->name}", $project);

        $this->aiService->analyzeAndGenerate($project->pic, $project);

        return back()->with('success', __('projects.completed'));
    }
}
