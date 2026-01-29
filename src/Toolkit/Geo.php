<?php

namespace Hks\Schema\Toolkit;

use Hks\Schema\Data\Types\CardinalDirection;
use Kirby\Toolkit\Str;

class Geo
{
    /**
     * Geographic coordinate notation (DMS).
     */
    public const string COORDINATE_FORMAT = '%d° %d\' %.2f"%s';

    /**
     * Radius of the earth in meters.
     */
    public const float EARTH_RADIUS = 6371000.0;

    /**
     * Great-circle distance between two point on a sphere (Haversine formular).
     */
    public static function distance(float $fromLatitude, float $fromLongitude, float $toLatitude, float $toLongitude): float
    {
        $radius = self::EARTH_RADIUS;

        $deltaLatitude = deg2rad($toLatitude - $fromLatitude);
        $deltaLongitude = deg2rad($toLongitude - $fromLongitude);

        $haversine = (sin($deltaLatitude / 2) ** 2) + cos(deg2rad($fromLatitude)) * cos(deg2rad($toLatitude)) * (sin($deltaLongitude / 2) ** 2);
        $distance = $radius * atan2(sqrt($haversine), sqrt(1 - $haversine));

        return $distance;
    }

    /**
     * Calculate degrees-minutes-seconds from decimalDesgrees.
     *
     * @return float[]
     */
    public static function dms(float $decimalDegrees, int $precision = 2): array
    {
        $decimalDegrees = abs($decimalDegrees);

        $degrees = floor($decimalDegrees);
        $minutes = floor(($decimalDegrees - $degrees) * 60);
        $seconds = round(($decimalDegrees - $degrees - ($minutes / 60)) * 3600, $precision);

        return [$degrees, $minutes, $seconds];
    }

    /**
     * Format the latitude of a geographic coordinate using DMS notation.
     */
    public static function formatLatitude(float $decimalDegrees, ?string $locale = null): string
    {
        [$degrees, $minutes, $seconds] = static::dms($decimalDegrees);

        $direction = $decimalDegrees >= 0 ? CardinalDirection::North : CardinalDirection::South;

        return sprintf(
            self::COORDINATE_FORMAT,
            $degrees,
            $minutes,
            $seconds,
            Str::substr($direction->toLocaleString($locale), length: 1),
        );
    }

    /**
     * Format the longitude of a geographic coordinate using DMS notation.
     */
    public static function formatLongitude(float $decimalDegrees, ?string $locale = null): string
    {
        [$degrees, $minutes, $seconds] = static::dms($decimalDegrees);

        $direction = $decimalDegrees >= 0 ? CardinalDirection::East : CardinalDirection::West;

        return sprintf(
            self::COORDINATE_FORMAT,
            $degrees,
            $minutes,
            $seconds,
            Str::substr($direction->toLocaleString($locale), length: 1),
        );
    }
}
