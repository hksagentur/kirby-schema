<?php

namespace Hks\Schema\Config\Methods;

use Hks\Schema\TestCase;
use Kirby\Cms\Collection;

class CollectionMethodsTest extends TestCase
{
    public function testMapWithKeysKeepsTheKeysReturnedByTheCallback(): void
    {
        $collection = new Collection(['a', 'b']);

        $mapped = $collection->mapWithKeys(fn (string $value) => [
            "2026-01-0{$value}" => strtoupper($value),
        ]);

        $this->assertInstanceOf(Collection::class, $mapped);
        $this->assertSame([
            '2026-01-0a' => 'A',
            '2026-01-0b' => 'B',
        ], $mapped->data());
    }

    public function testMapWithKeysKeepsPlainArrayValues(): void
    {
        $collection = new Collection(['monday']);

        $mapped = $collection->mapWithKeys(fn (string $day) => [
            '2026-01-09' => ['hours' => ['08:00-18:00']],
        ]);

        $this->assertSame(['2026-01-09'], $mapped->keys());
        $this->assertSame(['hours' => ['08:00-18:00']], $mapped->first());
    }
}
