<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Salon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ReviewController extends Controller
{
    public function store(Request $request, Salon $salon): RedirectResponse
    {
        Gate::authorize('create', [Review::class, $salon]);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:3000'],
        ]);

        // Enforce one review per user per salon.
        // The database unique index remains the final safeguard.
        $review = $request->user()->reviews()
            ->where('salon_id', $salon->id)
            ->first();

        if ($review) {
            return back()->withErrors([
                'review' => 'You have already reviewed this salon. Edit your existing review instead.',
            ]);
        }

        $request->user()->reviews()->create([
            'salon_id' => $salon->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'status' => 'visible',
        ]);

        return back()->with('success', 'Your review has been submitted.');
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        Gate::authorize('update', $review);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:3000'],
        ]);

        // Do not let the user change the review's owner, salon or status.
        $review->update([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return back()->with('success', 'Your review has been updated.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        Gate::authorize('delete', $review);

        $salonId = $review->salon_id;

        $review->delete();

        return redirect()
            ->route('salons.show', $salonId)
            ->with('success', 'Review deleted.');
    }
}