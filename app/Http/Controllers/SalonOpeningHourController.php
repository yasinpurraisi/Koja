<?php

namespace App\Http\Controllers;

use App\Models\Salon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class SalonOpeningHourController extends Controller
{
    public function update(Request $request, Salon $salon): RedirectResponse
    {
        Gate::authorize('update', $salon);

        $validated = $request->validate([
            'opening_hours' => ['required', 'array', 'size:7'],
            'opening_hours.*.day_of_week' => [
                'required',
                'integer',
                'between:1,7',
                'distinct',
            ],
            'opening_hours.*.is_closed' => [
                'required',
                'boolean',
            ],
            'opening_hours.*.opens_at' => [
                'nullable',
                'date_format:H:i',
            ],
            'opening_hours.*.closes_at' => [
                'nullable',
                'date_format:H:i',
            ],
        ]);

        foreach ($validated['opening_hours'] as $hours) {
            if (
                ! $hours['is_closed']
                && (
                    empty($hours['opens_at'])
                    || empty($hours['closes_at'])
                    || $hours['opens_at'] >= $hours['closes_at']
                )
            ) {
                throw ValidationException::withMessages([
                    'opening_hours' => 'Open days need valid opening and closing times, with closing later than opening.',
                ]);
            }
        }

        foreach ($validated['opening_hours'] as $hours) {
            $salon->openingHours()->updateOrCreate(
                ['day_of_week' => $hours['day_of_week']],
                [
                    'is_closed' => $hours['is_closed'],
                    'opens_at' => $hours['is_closed']
                        ? null
                        : $hours['opens_at'],
                    'closes_at' => $hours['is_closed']
                        ? null
                        : $hours['closes_at'],
                ],
            );
        }

        return back()->with('success', 'Opening hours updated.');
    }
}