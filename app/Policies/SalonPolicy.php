<?php

namespace App\Policies;

use App\Models\Salon;
use App\Models\User;

class SalonPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->status === 'active';
    }

    public function view(User $user, Salon $salon): bool
    {
        return $user->status === 'active'
            && (
                $salon->status === 'approved'
                || $user->isAdmin()
                || (
                    $user->role === 'owner'
                    && $salon->owner_id === $user->id
                )
            );
    }

    public function create(User $user): bool
    {
        return $user->status === 'active'
            && $user->role === 'owner'
            && !$user->salon()->exists();
    }

    public function update(User $user, Salon $salon): bool
    {
        if ($user->status !== 'active') {
            return false;
        }

        return $user->isAdmin()
            || (
                $user->role === 'owner'
                && $salon->owner_id === $user->id
            );
    }

    public function delete(User $user, Salon $salon): bool
    {
        return $user->status === 'active'
            && $user->role === 'owner'
            && $salon->owner_id === $user->id;
    }

    public function approve(User $user): bool
    {
        return $user->status === 'active'
            && $user->isAdmin();
    }

    public function suspend(User $user): bool
    {
        return $user->status === 'active'
            && $user->isAdmin();
    }
}