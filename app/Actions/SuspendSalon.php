<?php

namespace App\Actions;

use App\Models\Salon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SuspendSalon
{
    public function handle(Salon $salon): void
    {
        DB::transaction(function () use ($salon) {
            $salon = Salon::query()
                ->lockForUpdate()
                ->findOrFail($salon->id);

            if ($salon->status !== 'approved') {
                throw ValidationException::withMessages([
                    'salon' => 'Only approved salons can be suspended.',
                ]);
            }

            $salon->update([
                'status' => 'suspended',
            ]);
        });
    }
}