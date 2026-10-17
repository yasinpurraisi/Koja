<?php

namespace App\Actions;

use App\Models\Review;
use Illuminate\Validation\ValidationException;

class HideReview
{
    public function handle(Review $review): void
    {
        if ($review->status === 'hidden_by_suspension') {
            throw ValidationException::withMessages([
                'review' => 'This review is hidden because its author is suspended.',
            ]);
        }

        $review->update([
            'status' => 'hidden_by_admin',
        ]);
    }
}