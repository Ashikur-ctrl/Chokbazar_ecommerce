<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Seller;
use App\Models\SellerLedger;

class CommissionService
{
    /**
     * Calculate commission for a seller on an order.
     * Resolution: category override > seller default > system default.
     */
    public function calculateCommission(Seller $seller, float $amount): float
    {
        $sellerCommission = (float) $seller->commission_percentage;

        if ($sellerCommission > 0) {
            return round($amount * ($sellerCommission / 100), 2);
        }

        $defaultCommission = (float) config('shop.default_commission', 10);
        return round($amount * ($defaultCommission / 100), 2);
    }

    /**
     * Record commission deduction in seller ledger.
     */
    public function recordCommission(Order $order, Seller $seller, float $commissionAmount): void
    {
        $lastBalance = SellerLedger::where('seller_id', $seller->id)
            ->latest('id')
            ->value('balance') ?? 0;

        SellerLedger::create([
            'seller_id' => $seller->id,
            'type' => 'commission',
            'amount' => -$commissionAmount,
            'balance' => $lastBalance - $commissionAmount,
            'reference_type' => 'order',
            'reference_id' => $order->id,
            'notes' => "Commission deducted for order {$order->order_number}",
        ]);
    }

    /**
     * Record sale in seller ledger (credits the seller's balance).
     */
    public function recordSale(Order $order, Seller $seller, float $amount): void
    {
        $lastBalance = SellerLedger::where('seller_id', $seller->id)
            ->latest('id')
            ->value('balance') ?? 0;

        SellerLedger::create([
            'seller_id' => $seller->id,
            'type' => 'sale',
            'amount' => $amount,
            'balance' => $lastBalance + $amount,
            'reference_type' => 'order',
            'reference_id' => $order->id,
            'notes' => "Sale credited for order {$order->order_number}",
        ]);
    }

    /**
     * Record a payout.
     */
    public function recordPayout(Seller $seller, float $amount, int $payoutId): void
    {
        $lastBalance = SellerLedger::where('seller_id', $seller->id)
            ->latest('id')
            ->value('balance') ?? 0;

        SellerLedger::create([
            'seller_id' => $seller->id,
            'type' => 'payout',
            'amount' => -$amount,
            'balance' => $lastBalance - $amount,
            'reference_type' => 'payout',
            'reference_id' => $payoutId,
            'notes' => "Payout processed",
        ]);
    }
}
