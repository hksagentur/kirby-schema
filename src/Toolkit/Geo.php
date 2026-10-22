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
     * Returns the great-circle distance between two points in meters.
     *
     * Uses the Haversine formula on a spherical earth.
     */
    public static function distance(float $fromLatitude, float $fromLongitude, float $toLatitude, float $toLongitude): float
    {
        $radius = self::EARTH_RADIUS;

        $deltaLatitude = deg2rad($toLatitude - $fromLatitude);
        $deltaLongitude = deg2rad($toLongitude - $fromLongitude);

        $haversine = (sin($deltaLatitude / 2) ** 2) + cos(deg2rad($fromLatitude)) * cos(deg2rad($toLatitude)) * (sin($deltaLongitude / 2) ** 2);
        $distance = $radius * 2 * atan2(sqrt($haversine), sqrt(1 - $haversine));

        return $distance;
    }

    /**
     * Splits decimal degrees into degrees, minutes and seconds.
     *
     * @return array{float, float, float}
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
     * Formats a latitude in degrees, minutes and seconds, e.g. `52° 31' 12.03"N`.
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
     * Formats a longitude in degrees, minutes and seconds, e.g. `13° 24' 17.83"E`.
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
