<?php

namespace App\Http\Controllers;

use App\Models\BorrowTransaction;
use App\Models\Inventory;
use App\Models\Project;
use App\Models\ReturnTransaction;
use App\Models\User;
use App\Services\AiSuggestionService;
use App\Services\RiskScoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        private AiSuggestionService $aiService,
        private RiskScoreService $riskService,
    ) {}

    public function index(Request $request)
    {
        $user = auth()->user();
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Role Detection — hanya 2 role: Admin & PIC
        $isAdmin = $user->hasRole('Admin');
        $isPic   = $user->hasRole('PIC');

        // Stats (Role-Aware)
        $stats = $this->getStats($user, $startDate, $endDate);

        // Monthly borrow chart (role-filtered untuk PIC)
        $monthlyChart = $this->getMonthlyBorrowData($user);

        // Role-specific data
        $roleData = [];

        // ADMIN DATA — akses penuh
        if ($isAdmin) {
            $roleData['low_stock_items'] = Inventory::active()->lowStock()->with('category')->limit(5)->get();
            $roleData['pending_returns'] = ReturnTransaction::where('status', 'pending')
                ->with(['borrowItem.inventory', 'returnedBy'])->latest()->limit(5)->get();
            $roleData['recent_inventory_movements'] = \App\Models\AuditLog::where('action', 'like', '%inventory%')
                ->latest()->limit(5)->get();
            $roleData['pending_approvals'] = BorrowTransaction::pending()
                ->with(['requester', 'project'])->latest()->get();
            $roleData['active_borrow_transactions'] = BorrowTransaction::with(['requester', 'project', 'items.inventory'])
                ->latest()->limit(10)->get();
            $roleData['total_borrowers'] = BorrowTransaction::distinct('requested_by')->count();
        }

        // PIC DATA — hanya data milik PIC
        if ($isPic) {
            $roleData['my_active_projects'] = Project::active()->byPic($user->id)->latest()->get();
            $roleData['my_pending_borrows'] = BorrowTransaction::pending()
                ->where('requested_by', $user->id)->with('items.inventory')->latest()->get();
            $roleData['my_active_items'] = \App\Models\BorrowItem::whereHas('borrowTransaction', function ($q) use ($user) {
                $q->where('requested_by', $user->id)->where('status', 'borrowed');
            })->with('inventory')->get();
            $roleData['active_borrow_transactions'] = BorrowTransaction::with(['requester', 'project', 'items.inventory'])
                ->where('requested_by', $user->id)->latest()->limit(10)->get();
        }

        // AI insights (Admin only)
        $aiInsights = $isAdmin ? $this->aiService->getDashboardInsights() : [];

        // View Helpers
        $overdueCount   = $stats['overdue_count'];
        $pendingBorrows = $stats['pending_borrow'];

        return view('dashboard.index', compact(
            'user', 'stats', 'monthlyChart', 'aiInsights', 'roleData',
            'isPic', 'isAdmin',
            'overdueCount', 'pendingBorrows'
        ));
    }

    public function runAiAnalysis()
    {
        $this->aiService->runGlobalAnalysis();
        return back()->with('success', 'AI Analysis berhasil disinkronkan dengan data terkini!');
    }

    private function getStats($user, $startDate, $endDate): array
    {
        $isPic   = $user->hasRole('PIC');
        $isAdmin = $user->hasRole('Admin');

        $borrowQuery = BorrowTransaction::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        if ($isPic) {
            $borrowQuery->where('requested_by', $user->id);
        }

        return [
            'total_projects'  => Project::active()->when($isPic, fn($q) => $q->where('pic_id', $user->id))->count(),
            'total_borrowed'  => \App\Models\BorrowItem::whereIn('borrow_transaction_id', $borrowQuery->pluck('id'))->sum('quantity'),
            'overdue_count'   => BorrowTransaction::overdue()
                                    ->when($isPic, fn($q) => $q->where('requested_by', $user->id))
                                    ->count(),
            'total_inventory' => Inventory::active()->count(),
            'low_stock'       => Inventory::active()->lowStock()->count(),
            'pending_borrow'  => BorrowTransaction::pending()
                                    ->when($isPic, fn($q) => $q->where('requested_by', $user->id))
                                    ->count(),
            'my_holdings'     => $isPic ? \App\Models\BorrowItem::whereHas('borrowTransaction', fn($q) => $q->where('requested_by', $user->id)->where('status', 'borrowed'))->sum('quantity') : 0,
        ];
    }

    private function getMonthlyBorrowData($user): array
    {
        $isPic = $user->hasRole('PIC');

        $months = collect(range(5, 0))->map(function ($i) use ($user, $isPic) {
            $date = now()->subMonths($i);
            return [
                'month' => $date->format('M Y'),
                'count' => BorrowTransaction::whereYear('created_at', $date->year)
                                ->whereMonth('created_at', $date->month)
                                ->when($isPic, fn($q) => $q->where('requested_by', $user->id))
                                ->count(),
            ];
        });

        return [
            'labels' => $months->pluck('month')->toArray(),
            'data'   => $months->pluck('count')->toArray(),
        ];
    }
}
