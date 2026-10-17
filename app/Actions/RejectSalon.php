<?php

namespace App\Actions;

use App\Models\Salon;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RejectSalon
{
    public function handle(Salon $salon, User $admin): void
    {
        DB::transaction(function () use ($salon, $admin) {
            $salon = Salon::query()
                ->lockForUpdate()
                ->findOrFail($salon->id);

            if ($salon->status !== 'pending') {
                throw ValidationException::withMessages([
                    'salon' => 'Only pending salons can be rejected.',
                ]);
            }

            $salon->update([
                'status' => 'rejected',
                'approved_by' => $admin->id,
                'reviewed_at' => now(),
            ]);
        });
    }
}