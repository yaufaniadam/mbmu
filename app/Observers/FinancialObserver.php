<?php

namespace App\Observers;

use App\Models\OperatingExpense;
use App\Models\Remittance;
use App\Models\SppgIncomingFund;

class FinancialObserver
{
    // OPTIONAL: Only update balance after the transaction commits successfully
    // implements ShouldHandleEventsAfterCommit

    public function creating($model)
    {
        if ($model instanceof SppgIncomingFund) {
            $model->sppg()->increment('balance', $model->amount);
        }

        if ($model instanceof OperatingExpense) {
            $model->sppg()->decrement('balance', $model->amount);
        }

        if ($model instanceof Remittance) {
            $model->bill->sppg()->decrement('balance', $model->bill->amount);
        }
    }

    public function updating($model)
    {
        // Handle Amount Changes (If user edits amount directly without the Revision system)
        if ($model->isDirty('amount')) {
            $diff = $model->amount - $model->getOriginal('amount');

            if ($model instanceof SppgIncomingFund) {
                $model->sppg()->increment('balance', $diff);
            }
            // Add logic for Expense if needed
        }

        if ($model instanceof Remittance) {
            if ($model->isDirty('status') && $model->status === 'rejected') {
                $model->bill->sppg()->increment('balance', $model->bill->amount);
            }
        }
    }

    public function deleting($model)
    {
        // PREVENT DOUBLE DEDUCTION BUG:
        // If the model is already soft-deleted, and we are now force-deleting it,
        // do NOT deduct the balance again. The balance was already deduced
        // when it was soft-deleted the first time.
        if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting() && $model->trashed()) {
            return;
        }

        if ($model instanceof SppgIncomingFund) {
            $model->sppg()->decrement('balance', $model->amount);
        }

        if ($model instanceof OperatingExpense) {
            $model->sppg()->increment('balance', $model->amount);
        }
    }

    /**
     * HANDLE RESTORE:
     * If you mistakenly deleted a record and click "Restore",
     * the balance must be added back.
     */
    public function restored($model)
    {
        if ($model instanceof SppgIncomingFund) {
            $model->sppg()->increment('balance', $model->amount);
        }

        if ($model instanceof OperatingExpense) {
            $model->sppg()->decrement('balance', $model->amount);
        }
    }
}
