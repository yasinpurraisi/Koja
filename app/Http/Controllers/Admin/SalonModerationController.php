<?php

namespace App\Http\Controllers\Admin;

use App\Actions\ApproveSalon;
use App\Actions\RejectSalon;
use App\Actions\RestoreSalon;
use App\Actions\SuspendSalon;
use App\Http\Controllers\Controller;
use App\Models\Salon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class SalonModerationController extends Controller
{
    public function approve(Salon $salon, ApproveSalon $action): RedirectResponse
    {
        Gate::authorize('approve', Salon::class);

        $action->handle($salon, request()->user());

        return back()->with('success', 'Salon approved.');
    }

    public function reject(Salon $salon, RejectSalon $action): RedirectResponse
    {
        Gate::authorize('approve', Salon::class);

        $action->handle($salon, request()->user());

        return back()->with('success', 'Salon rejected.');
    }

    public function suspend(Salon $salon, SuspendSalon $action): RedirectResponse
    {
        Gate::authorize('suspend', Salon::class);

        $action->handle($salon);

        return back()->with('success', 'Salon suspended.');
    }

    public function restore(Salon $salon, RestoreSalon $action): RedirectResponse
    {
        Gate::authorize('suspend', Salon::class);

        $action->handle($salon);

        return back()->with('success', 'Salon restored.');
    }
}