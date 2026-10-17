<?php

namespace App\Http\Controllers;

use App\Models\Salon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SalonController extends Controller
{
    // Public salon directory.
    public function index(Request $request): View
    {
        $salons = Salon::query()
            ->where('status', 'approved')
            ->when($request->filled('city'), function ($query) use ($request) {
                $query->where('city', $request->string('city')->toString());
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                });
            })
            ->withCount([
                'reviews' => fn ($query) => $query
                    ->where('status', 'visible')
                    ->whereHas('user', fn ($users) => $users->where('status', 'active')),
            ])
            ->withAvg([
                'reviews' => fn ($query) => $query
                    ->where('status', 'visible')
                    ->whereHas('user', fn ($users) => $users->where('status', 'active')),
            ], 'rating')
            ->with([
                'reviews' => fn ($query) => $query
                    ->where('status', 'visible')
                    ->whereHas('user', fn ($users) => $users->where('status', 'active')),
            ])
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('salons.index', compact('salons'));
    }

    // Public details page. Only approved salons are visible here.
    public function show(Salon $salon): View
    {
        abort_unless($salon->status === 'approved', 404);

        $salon->load([
            'owner',
            'openingHours',
            'reviews' => fn ($query) => $query
                ->where('status', 'visible')
                ->whereHas('user', fn ($users) => $users->where('status', 'active'))
                ->with('user')
                ->latest(),
        ]);

        return view('salons.show', compact('salon'));
    }

    // Owner creates their one salon.
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Salon::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $salon = $request->user()->salon()->create([
            ...$validated,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('owner.salon.edit', $salon)
            ->with('success', 'Salon submitted for admin approval.');
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', Salon::class);

        return view('owner.salons.create');
    }

    public function edit(Salon $salon): View
    {
        Gate::authorize('update', $salon);

        $salon->load('openingHours');

        return view('owner.salons.edit', compact('salon'));
    }

    // Owner edits their salon.
    public function update(Request $request, Salon $salon): RedirectResponse
    {
        Gate::authorize('update', $salon);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        // Never accept status, owner_id or approved_by from this form.
        $salon->update($validated);

        return back()->with('success', 'Salon details updated.');
    }

    // Owner permanently deletes their salon.
    public function destroy(Salon $salon): RedirectResponse
    {
        Gate::authorize('delete', $salon);

        $salon->delete();

        return redirect()
            ->route('salons.index')
            ->with('success', 'Salon and its associated data were deleted.');
    }

    public function dashboard(Request $request): View
    {
        abort_unless($request->user()->role === 'owner', 403);

        $salon = $request->user()->salon()
            ->with([
                'openingHours',
                'reviews' => fn ($query) => $query
                    ->with('user')
                    ->latest(),
            ])
            ->first();

        return view('owner.dashboard', compact('salon'));
    }

}