<?php

namespace App\Actions;

use App\Models\Review;
use Illuminate\Validation\ValidationException;

class RestoreReview
{
    public function handle(Review $review): void
    {
        if ($review->status !== 'hidden_by_admin') {
            throw ValidationException::withMessages([
                'review' => 'Only reviews hidden by an admin can be restored here.',
            ]);
        }

        if ($review->user->status !== 'active') {
            throw ValidationException::withMessages([
                'review' => 'The review author must be active before this review can be restored.',
            ]);
        }

        $review->update([
            'status' => 'visible',
        ]);
    }
}