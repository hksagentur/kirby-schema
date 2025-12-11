<?php

namespace Hks\Schema\Formatter;

use Hks\Schema\Data\GeoCoordinate;
use Kirby\Toolkit\I18n;

/**
 * @extends StructuredDataFormatter<GeoCoordinate>
 */
class GeoCoordinateTextFormatter extends StructuredDataFormatter
{
    public function getAttributes(mixed $coordinate): array
    {
        return [
            'latitude' => fn () => $this->formatCoordinate(
                $coordinate->latitude(),
                $coordinate->latitude() >= 0 ? 'N' : 'S',
            ),
            'longitude' => fn () => $this->formatCoordinate(
                $coordinate->longitude(),
                $coordinate->longitude() >= 0 ? 'E' : 'W',
            ),
        ];
    }

    public function compose(array $content): string
    {
        return I18n::template('hksagentur.schema.formatter.geoCoordinate', replace: $content);
    }

    /** @param ('E'|'S'|'W'|'N') $direction */
    protected function formatCoordinate(float $decimalDegrees, string $direction): string
    {
        return sprintf('%d° %d\' %.2f"%s', ...$this->decimalDegreesToDMS($decimalDegrees), $direction);
    }

    /** @return float[] */
    protected function decimalDegreesToDMS(float $decimalDegrees, int $precision = 2): array
    {
        $decimalDegrees = abs($decimalDegrees);

        $degrees = floor($decimalDegrees);
        $minutes = floor(($decimalDegrees - $degrees) * 60);
        $seconds = round(($decimalDegrees - $degrees - ($minutes / 60)) * 3600, $precision);

        return [$degrees, $minutes, $seconds];
    }
}
