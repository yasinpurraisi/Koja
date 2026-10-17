<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->status === 'active'
            && $user->isAdmin();
    }

    public function view(User $user, User $target): bool
    {
        return $user->status === 'active'
            && (
                $user->isAdmin()
                || $user->id === $target->id
            );
    }

    public function suspend(User $user, User $target): bool
    {
        return $user->status === 'active'
            && $user->isAdmin()
            && $target->role !== 'admin';
    }

    public function restore(User $user, User $target): bool
    {
        return $user->status === 'active'
            && $user->isAdmin()
            && $target->role !== 'admin';
    }
}