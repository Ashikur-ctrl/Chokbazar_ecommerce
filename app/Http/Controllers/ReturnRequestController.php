<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ReturnRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReturnRequestController extends Controller
{
    public function index(): View
    {
        $returnRequests = ReturnRequest::with(['order', 'user', 'orderItem'])->latest()->paginate(20);
        return view('admin.returns.index', compact('returnRequests'));
    }

    public function myReturns(): View
    {
        $returnRequests = ReturnRequest::where('user_id', auth()->id())->with('order')->latest()->paginate(20);
        return view('orders.returns', compact('returnRequests'));
    }

    public function store(Request $request, Order $order): RedirectResponse
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'order_item_id' => 'nullable|exists:order_items,id',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
        ]);

        ReturnRequest::create($validated + [
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Return request submitted. We will review it shortly.');
    }

    public function approve(ReturnRequest $returnRequest): RedirectResponse
    {
        $returnRequest->update(['status' => 'approved']);
        return back()->with('success', 'Return request approved.');
    }

    public function reject(Request $request, ReturnRequest $returnRequest): RedirectResponse
    {
        $returnRequest->update([
            'status' => 'rejected',
            'admin_note' => $request->input('admin_note'),
        ]);
        return back()->with('success', 'Return request rejected.');
    }

    public function refund(ReturnRequest $returnRequest): RedirectResponse
    {
        $returnRequest->update([
            'status' => 'refunded',
            'refunded_at' => now(),
        ]);

        $returnRequest->order->update(['payment_status' => 'refunded']);

        return back()->with('success', 'Refund processed.');
    }
}
