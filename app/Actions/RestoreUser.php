<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class RestoreUser
{
    public function handle(User $target): void
    {
        DB::transaction(function () use ($target) {
            $target = User::query()
                ->lockForUpdate()
                ->findOrFail($target->id);

            if ($target->status !== 'suspended') {
                return;
            }

            $target->update([
                'status' => 'active',
            ]);

            // Restore only reviews hidden because of suspension.
            $target->reviews()
                ->where('status', 'hidden_by_suspension')
                ->update([
                    'status' => 'visible',
                    'updated_at' => now(),
                ]);
        });
    }
}