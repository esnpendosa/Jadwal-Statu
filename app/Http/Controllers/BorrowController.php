<?php

namespace App\Http\Controllers;

use App\Http\Requests\BorrowRequest;
use App\Mail\BorrowNotificationMail;
use App\Models\BorrowTransaction;
use App\Models\Inventory;
use App\Models\Project;
use App\Services\BorrowService;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BorrowController extends Controller
{
    public function __construct(private BorrowService $service) {}

    public function index(Request $request)
    {
        $user  = auth()->user();
        $isPic = $user->hasRole('PIC');

        $borrows = BorrowTransaction::with(['project', 'items.inventory', 'requester', 'approver'])
            ->when($isPic, fn($q) => $q->where('requested_by', $user->id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->project_id, fn($q) => $q->where('project_id', $request->project_id))
            ->when($request->search, fn($q) => $q->where(function($sq) use ($request) {
                $sq->where('code', 'like', "%{$request->search}%")
                   ->orWhereHas('requester', fn($u) => $u->where('name', 'like', "%{$request->search}%"));
            }))
            ->latest()
            ->paginate(15)->withQueryString();

        $projects = Project::active()->get();
        $overdueCount = BorrowTransaction::overdue()->when($isPic, fn($q) => $q->where('requested_by', $user->id))->count();

        return view('borrow.index', compact('borrows', 'projects', 'overdueCount'));
    }

    public function create()
    {
        $projects    = Project::active()->with('pic')->get();
        $inventories = Inventory::where('stock_available', '>', 0)->with('category')->get();
        return view('borrow.create', compact('projects', 'inventories'));
    }

    public function store(BorrowRequest $request)
    {
        try {
            $borrow = $this->service->createBorrow($request->validated());
            AuditLog::log('create', "Created borrow request: {$borrow->code}", $borrow);

            return redirect()->route('borrow.show', $borrow)
                ->with('success', __('borrow.created'));
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(BorrowTransaction $borrow)
    {
        $borrow->load(['project.pic', 'items.inventory', 'requester', 'approver', 'returnTransactions.returnedBy']);
        return view('borrow.show', compact('borrow'));
    }

    public function print(BorrowTransaction $borrow)
    {
        $borrow->load(['project', 'items.inventory', 'requester', 'approver']);
        return view('borrow.print', compact('borrow'));
    }

    public function approve(BorrowTransaction $borrow)
    {
        if ($borrow->status !== 'pending') {
            return back()->with('error', __('borrow.invalid_status'));
        }

        try {
            $this->service->approveBorrow($borrow);
            AuditLog::log('approve', "Approved borrow: {$borrow->code}", $borrow);
            return back()->with('success', __('borrow.approved'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, BorrowTransaction $borrow)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);

        $this->service->rejectBorrow($borrow, $request->reason);
        AuditLog::log('reject', "Rejected borrow: {$borrow->code}", $borrow);

        return back()->with('success', __('borrow.rejected'));
    }

    public function sendNotification(BorrowTransaction $borrow)
    {
        try {
            $borrow->load(['project', 'items.inventory', 'requester']);
            $type = $borrow->is_overdue ? 'overdue' : ($borrow->status === 'borrowed' ? 'approved' : 'created');
            Mail::to($borrow->requester->email)
                ->send(new BorrowNotificationMail($borrow, $type));
            AuditLog::log('notify', "Sent {$type} notification for borrow: {$borrow->code}", $borrow);
            return back()->with('success', 'Notifikasi berhasil dikirim ke ' . $borrow->requester->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim notifikasi: ' . $e->getMessage());
        }
    }
}
