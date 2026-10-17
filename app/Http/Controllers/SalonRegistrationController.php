<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SalonRegistrationController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Admin accounts cannot register salons.');
        }

        if ($user->salon()->exists()) {
            return redirect()
                ->route('owner.dashboard')
                ->with('error', 'Your account already has a salon listing.');
        }

        return view('salons.register-owner');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Admin accounts cannot register salons.');
        }

        if ($user->salon()->exists()) {
            return redirect()
                ->route('owner.dashboard')
                ->with('error', 'Your account already has a salon listing.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'phone' => ['required', 'string', 'max:30'],
            'city' => ['required', 'string', 'max:120'],
            'address' => ['required', 'string', 'max:1000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        DB::transaction(function () use ($user, $validated): void {
            $user->salon()->create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'phone' => $validated['phone'],
                'city' => $validated['city'],
                'address' => $validated['address'],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'status' => 'pending',
            ]);

            $user->update([
                'role' => 'owner',
            ]);
        });

        return redirect()
            ->route('owner.dashboard')
            ->with(
                'success',
                'Your salon has been submitted for admin approval.'
            );
    }
}