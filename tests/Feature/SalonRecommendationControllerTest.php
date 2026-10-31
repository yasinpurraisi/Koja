<?php

use App\Models\Review;
use App\Models\Salon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('offers coordinate-based recommendations from the salon directory', function () {
    $response = $this->get(route('salons.index'));

    $response->assertOk()
        ->assertSee(route('salons.recommended'))
        ->assertSee('recommendation-latitude')
        ->assertSee('recommendation-longitude')
        ->assertSee('Use my current location');
});

it('shows approved salons in recommendation score order', function () {
    Salon::factory()->approved()->create([
        'name' => 'Unrated at origin',
        'latitude' => 0,
        'longitude' => 0,
    ]);

    $highRatedSalon = Salon::factory()->approved()->create([
        'name' => 'Top rated nearby',
        'latitude' => 0.0001,
        'longitude' => 0,
    ]);

    Review::factory()->count(5)->create([
        'salon_id' => $highRatedSalon->id,
        'rating' => 5,
    ]);

    $response = $this->get(route('salons.recommended', [
        'latitude' => 0,
        'longitude' => 0,
    ]));

    $response->assertOk()
        ->assertSeeInOrder(['Top rated nearby', 'Unrated at origin'])
        ->assertSee('Match score')
        ->assertSee('89.9')
        ->assertSee('80.0');
});

it('shows an empty state when no salons can be recommended', function () {
    $response = $this->get(route('salons.recommended', [
        'latitude' => 0,
        'longitude' => 0,
    ]));

    $response->assertOk()
        ->assertSee('No nearby salons to recommend yet');
});

it('rejects coordinates outside valid latitude and longitude ranges', function () {
    $response = $this->get(route('salons.recommended', [
        'latitude' => 91,
        'longitude' => 181,
    ]));

    $response->assertSessionHasErrors(['latitude', 'longitude']);
});
