<?php

namespace App\Http\Controllers\Admin;

use App\Actions\HideReview;
use App\Actions\RestoreReview;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ReviewModerationController extends Controller
{
    public function hide(Review $review, HideReview $action): RedirectResponse
    {
        Gate::authorize('hide', Review::class);

        $action->handle($review);

        return back()->with('success', 'Review hidden.');
    }

    public function restore(Review $review, RestoreReview $action): RedirectResponse
    {
        Gate::authorize('restore', Review::class);

        $action->handle($review);

        return back()->with('success', 'Review restored.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        Gate::authorize('delete', $review);

        $review->delete();

        return back()->with('success', 'Review permanently deleted.');
    }
}