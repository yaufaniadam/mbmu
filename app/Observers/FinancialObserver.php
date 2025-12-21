<?php

namespace App\Observers;

use App\Models\OperatingExpense;
use App\Models\Remittance;
use App\Models\SppgIncomingFund;

class FinancialObserver
{
    public function creating($model)
    {
        // 1. Incoming Funds (Always adds to balance)
        if ($model instanceof SppgIncomingFund) {
            $model->sppg()->increment('balance', $model->amount);
        }

        // 2. Operating Expense (Always reduces balance)
        if ($model instanceof OperatingExpense) {
            $model->sppg()->decrement('balance', $model->amount);
        }

        // 3. Remittance (PAYMENT LOGIC UPDATED)
        if ($model instanceof Remittance) {
            // ONLY deduct balance if the bill is charged to the SPPG ('billed_to_type' = 'sppg')
            // If 'billed_to_type' is 'pengusul', we touch nothing.
            if ($model->bill->billed_to_type === 'sppg') {
                $model->bill->sppg()->decrement('balance', $model->bill->amount);
            }
        }
    }

    public function updating($model)
    {
        // Handle Rejected Payments (Refund)
        if ($model instanceof Remittance) {
            if ($model->isDirty('status') && $model->status === 'rejected') {
                // ONLY refund if it was an SPPG bill
                if ($model->bill->billed_to_type === 'sppg') {
                    $model->bill->sppg()->increment('balance', $model->bill->amount);
                }
            }
        }
    }

    // Note: I included the Soft Delete fix we discussed previously here too
    public function deleting($model)
    {
        if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting() && $model->trashed()) {
            return;
        }

        if ($model instanceof SppgIncomingFund) {
            $model->sppg()->decrement('balance', $model->amount);
        }

        if ($model instanceof OperatingExpense) {
            $model->sppg()->increment('balance', $model->amount);
        }

        // Note: Usually Remittances aren't deleted directly (they are rejected),
        // but if you do delete them, apply the same check:
        if ($model instanceof Remittance) {
            if ($model->bill->billed_to_type === 'sppg') {
                $model->bill->sppg()->increment('balance', $model->bill->amount);
            }
        }
    }
}
