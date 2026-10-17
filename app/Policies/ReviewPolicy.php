<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\Salon;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->status === 'active';
    }

    public function view(User $user, Review $review): bool
    {
        return $user->status === 'active'
            && (
                $user->isAdmin()
                || $review->user_id === $user->id
                || (
                    $review->salon->owner_id === $user->id
                    && $user->role === 'owner'
                )
            );
    }

    public function create(User $user, Salon $salon): bool
    {
        return $user->status === 'active'
            && $user->role !== 'admin'
            && $salon->status === 'approved'
            && $salon->owner_id !== $user->id;
    }

    public function update(User $user, Review $review): bool
    {
        return $user->status === 'active'
            && $review->user_id === $user->id;
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->status === 'active'
            && (
                $user->isAdmin()
                || $review->user_id === $user->id
            );
    }

    public function hide(User $user): bool
    {
        return $user->status === 'active'
            && $user->isAdmin();
    }

    public function restore(User $user): bool
    {
        return $user->status === 'active'
            && $user->isAdmin();
    }
}