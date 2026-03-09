<?php

namespace App\Http\Controllers;

use App\Models\BorrowTransaction;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Project;
use App\Models\ReturnTransaction;
use App\Models\RiskScore;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function inventory(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $inventories = Inventory::active()->with('category')
            ->when($request->category, fn($q) => $q->where('category_id', $request->category))
            ->when($request->condition, fn($q) => $q->where('condition', $request->condition))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->get();

        return view('reports.inventory', compact('inventories', 'categories'));
    }

    public function borrow(Request $request)
    {
        $projects = Project::orderBy('name')->get();
        $borrows = BorrowTransaction::with(['project', 'items.inventory', 'requester'])
            ->when($request->project_id, fn($q) => $q->where('project_id', $request->project_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate(20)->withQueryString();

        return view('reports.borrow', compact('borrows', 'projects'));
    }

    public function risk(Request $request)
    {
        $roles = \Spatie\Permission\Models\Role::all();
        $users = User::with(['roles', 'projectsAsPic'])
            ->when($request->role, fn($q) => $q->whereHas('roles', fn($rq) => $rq->where('name', $request->role)))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->get()
            ->sortByDesc('risk_score');

        $scores = RiskScore::with(['user', 'project', 'riskRule'])
            ->when($request->user_id, fn($q) => $q->where('user_id', $request->user_id))
            ->latest()->paginate(20)->withQueryString();

        return view('reports.risk', compact('scores', 'users', 'roles'));
    }

    public function export(Request $request, string $type)
    {
        if ($type === 'inventory') {
            $inventories = Inventory::active()->with('category')
                ->when($request->category, fn($q) => $q->where('category_id', $request->category))
                ->when($request->condition, fn($q) => $q->where('condition', $request->condition))
                ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
                ->get();

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.inventory', [
                'inventories' => $inventories,
                'date' => now()->format('d F Y H:i'),
                'filters' => [
                    'category' => $request->category ? Category::find($request->category)?->name : 'All',
                    'condition' => $request->condition ?: 'All'
                ]
            ]);

            return $pdf->download('inventory-report-' . now()->format('Y-m-d') . '.pdf');
        }

        if ($type === 'borrow') {
            $borrows = BorrowTransaction::with(['project', 'requester', 'items.inventory'])
                ->when($request->project_id, fn($q) => $q->where('project_id', $request->project_id))
                ->when($request->status, fn($q) => $q->where('status', $request->status))
                ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
                ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
                ->latest()
                ->get();

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.borrow', [
                'borrows' => $borrows,
                'date' => now()->format('d F Y H:i'),
            ]);

            return $pdf->download('borrow-report-' . now()->format('Y-m-d') . '.pdf');
        }

        if ($type === 'risk') {
            $scores = RiskScore::with(['user', 'project', 'riskRule'])
                ->when($request->user_id, fn($q) => $q->where('user_id', $request->user_id))
                ->latest()->get();

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.risk', [
                'scores' => $scores,
                'date' => now()->format('d F Y H:i'),
            ]);

            return $pdf->download('risk-analysis-report-' . now()->format('Y-m-d') . '.pdf');
        }

        return back()->with('info', 'Export feature for ' . $type . ' coming soon');
    }
}

