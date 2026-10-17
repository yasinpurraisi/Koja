<?php

namespace App\Http\Controllers\Admin;

use App\Actions\RestoreUser;
use App\Actions\SuspendUser;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class UserModerationController extends Controller
{
    public function suspend(User $user, SuspendUser $action): RedirectResponse
    {
        Gate::authorize('suspend', $user);

        $action->handle($user);

        return back()->with('success', 'User suspended.');
    }

    public function restore(User $user, RestoreUser $action): RedirectResponse
    {
        Gate::authorize('restore', $user);

        $action->handle($user);

        return back()->with('success', 'User restored.');
    }
}