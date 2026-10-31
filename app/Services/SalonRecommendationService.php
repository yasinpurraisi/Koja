<?php

namespace App\Services;

use App\Models\Salon;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class SalonRecommendationService
{
    private const EARTH_RADIUS_KM = 6371.0;

    private const DISTANCE_SCALE_KM = 5.0;

    private const DISTANCE_WEIGHT = 0.60;

    private const RATING_WEIGHT = 0.40;

    private const PRIOR_RATING = 3.0;

    private const MINIMUM_REVIEWS = 5.0;

    /**
     * Recommend the highest-scoring approved salons.
     *
     * Each salon receives:
     * - distance_km
     * - average_rating
     * - review_count
     * - adjusted_rating
     * - recommendation_score
     *
     * @return Collection<int, Salon>
     */
    public function recommend(
        float $latitude,
        float $longitude,
        int $limit = 10
    ): Collection {
        $this->validateLocation($latitude, $longitude);

        $limit = max(1, min($limit, 50));

        // Fetch approved salons with valid coordinates.
        $salons = Salon::query()
            ->where('status', 'approved')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->withAvg([
                'reviews as average_rating' => function ($query) {
                    $query->where('status', 'visible');
                },
            ], 'rating')
            ->withCount([
                'reviews as review_count' => function ($query) {
                    $query->where('status', 'visible');
                },
            ])
            ->get();

        return $salons
            ->map(function (Salon $salon) use ($latitude, $longitude) {
                $distance = $this->calculateDistance(
                    $latitude,
                    $longitude,
                    (float) $salon->latitude,
                    (float) $salon->longitude
                );

                $averageRating = $salon->average_rating !== null
                    ? (float) $salon->average_rating
                    : null;

                $reviewCount = (int) $salon->review_count;

                $adjustedRating = $this->calculateAdjustedRating(
                    $averageRating,
                    $reviewCount
                );

                $distanceScore = exp(
                    -$distance / self::DISTANCE_SCALE_KM
                );

                $ratingScore = ($adjustedRating - 1.0) / 4.0;

                $recommendationScore =
                    self::DISTANCE_WEIGHT * $distanceScore
                    + self::RATING_WEIGHT * $ratingScore;

                $salon->setAttribute(
                    'distance_km',
                    round($distance, 2)
                );

                $salon->setAttribute(
                    'average_rating',
                    $averageRating !== null
                        ? round($averageRating, 2)
                        : null
                );

                $salon->setAttribute('review_count', $reviewCount);

                $salon->setAttribute(
                    'adjusted_rating',
                    round($adjustedRating, 2)
                );

                $salon->setAttribute(
                    'recommendation_score',
                    round($recommendationScore, 4)
                );

                return $salon;
            })
            ->sort(function (Salon $a, Salon $b) {
                // Highest recommendation score first.
                $scoreComparison =
                    $b->recommendation_score <=> $a->recommendation_score;

                // Prefer the closer salon when scores tie.
                return $scoreComparison !== 0
                    ? $scoreComparison
                    : $a->distance_km <=> $b->distance_km;
            })
            ->take($limit)
            ->values();
    }

    /**
     * Calculate Haversine distance in kilometres.
     */
    private function calculateDistance(
        float $latitude1,
        float $longitude1,
        float $latitude2,
        float $longitude2
    ): float {
        $lat1 = deg2rad($latitude1);
        $lon1 = deg2rad($longitude1);
        $lat2 = deg2rad($latitude2);
        $lon2 = deg2rad($longitude2);

        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;

        $a = sin($deltaLat / 2) ** 2
            + cos($lat1)
            * cos($lat2)
            * sin($deltaLon / 2) ** 2;

        // Avoid floating-point rounding errors.
        $a = max(0.0, min(1.0, $a));

        $centralAngle = 2 * atan2(
            sqrt($a),
            sqrt(1.0 - $a)
        );

        return self::EARTH_RADIUS_KM * $centralAngle;
    }

    /**
     * Calculate a Bayesian-style adjusted rating on a 1–5 scale.
     */
    private function calculateAdjustedRating(
        ?float $averageRating,
        int $reviewCount
    ): float {
        // A salon without reviews receives the prior rating.
        if ($averageRating === null || $reviewCount === 0) {
            return self::PRIOR_RATING;
        }

        return (
            ($reviewCount * $averageRating)
            + (self::MINIMUM_REVIEWS * self::PRIOR_RATING)
        ) / ($reviewCount + self::MINIMUM_REVIEWS);
    }

    /**
     * Validate the supplied coordinates.
     */
    private function validateLocation(
        float $latitude,
        float $longitude
    ): void {
        if (
            ! is_finite($latitude)
            || ! is_finite($longitude)
            || $latitude < -90
            || $latitude > 90
            || $longitude < -180
            || $longitude > 180
        ) {
            throw new InvalidArgumentException(
                'Invalid latitude or longitude.'
            );
        }
    }
}
