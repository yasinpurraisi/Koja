<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SuspendUser
{
    public function handle(User $target): void
    {
        DB::transaction(function () use ($target) {
            $target = User::query()
                ->lockForUpdate()
                ->findOrFail($target->id);

            if ($target->role === 'admin') {
                throw ValidationException::withMessages([
                    'user' => 'Administrators cannot be suspended.',
                ]);
            }

            // if user is owner also suspand salon ?
            // if ($target->role === 'owner') {
            //     $target->salon()->update([
            //         'status' => 'suspanded'
            //     ]);
            // }

            if ($target->status === 'suspended') {
                return;
            }

            $target->update([
                'status' => 'suspended',
            ]);

            // Hide only currently visible reviews.
            // Reviews hidden by an admin remain hidden.
            $target->reviews()
                ->where('status', 'visible')
                ->update([
                    'status' => 'hidden_by_suspension',
                    'updated_at' => now(),
                ]);
        });
    }
}