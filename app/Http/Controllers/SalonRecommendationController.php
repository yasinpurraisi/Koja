<?php

namespace App\Http\Controllers;

use App\Services\SalonRecommendationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalonRecommendationController extends Controller
{
    public function recommended(
        Request $request,
        SalonRecommendationService $service
    ): View {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $salons = $service->recommend(
            (float) $validated['latitude'],
            (float) $validated['longitude'],
            10
        );

        return view('salons.recommended', ['salons' => $salons]);
    }
}
