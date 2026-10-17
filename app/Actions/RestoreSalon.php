<?php

namespace App\Actions;

use App\Models\Salon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RestoreSalon
{
    public function handle(Salon $salon): void
    {
        DB::transaction(function () use ($salon) {
            $salon = Salon::query()
                ->lockForUpdate()
                ->findOrFail($salon->id);

            if ($salon->status !== 'suspended') {
                throw ValidationException::withMessages([
                    'salon' => 'Only suspended salons can be restored.',
                ]);
            }

            $salon->update([
                'status' => 'approved',
            ]);
        });
    }
}