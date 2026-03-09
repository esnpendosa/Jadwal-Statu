<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReturnRequest;
use App\Models\BorrowTransaction;
use App\Models\ReturnTransaction;
use App\Services\ReturnService;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    public function __construct(private ReturnService $service) {}

    public function index(Request $request)
    {
        $returns = ReturnTransaction::with([
                'borrowTransaction.project',
                'borrowItem.inventory',
                'returnedBy',
                'receivedBy',
            ])
            ->when($request->search, fn($q) => $q->where('code', 'like', "%{$request->search}%"))
            ->when($request->condition, fn($q) => $q->where('condition_notes', 'like', "%{$request->condition}%"))
            ->latest()
            ->paginate(15)->withQueryString();

        return view('return.index', compact('returns'));
    }

    public function create(BorrowTransaction $borrow, Request $request)
    {
        if (!in_array($borrow->status, ['borrowed', 'approved'])) {
            return redirect()->route('borrow.show', $borrow)
                ->with('error', __('return.invalid_borrow_status'));
        }

        $itemId = $request->query('item_id');
        $borrow->load(['project', 'items.inventory', 'requester']);
        
        $targetItem = null;
        if ($itemId) {
            $targetItem = $borrow->items->find($itemId);
        } else {
            // Default to first item if not specified, for backward compatibility or simple case
            $targetItem = $borrow->items->where('quantity_returned', '<', 'quantity')->first();
        }

        if (!$targetItem) {
            return redirect()->route('borrow.show', $borrow)->with('error', 'No items available to return.');
        }

        return view('return.create', compact('borrow', 'targetItem'));
    }

    public function store(ReturnRequest $request, BorrowTransaction $borrow)
    {
        try {
            $return = $this->service->processReturn($borrow, $request->validated());
            AuditLog::log('return', "Processed return: {$return->code}", $return);

            return redirect()->route('return.show', $return)
                ->with('success', __('return.processed'));
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(ReturnTransaction $return)
    {
        $return->load(['borrowTransaction.project', 'borrowItem.inventory', 'returnedBy', 'receivedBy', 'riskScores.riskRule']);
        return view('return.show', compact('return'));
    }

    public function print(ReturnTransaction $return)
    {
        $return->load(['borrowTransaction.project', 'borrowItem.inventory', 'returnedBy', 'receivedBy', 'riskScores.riskRule']);
        return view('return.print', compact('return'));
    }

    public function verify(ReturnTransaction $return)
    {
        $this->service->verifyReturn($return);
        AuditLog::log('verify', "Verified return: {$return->code}", $return);

        return back()->with('success', __('return.verified'));
    }
}
