<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Schemable;
use Hks\Schema\TestCase;

class PostalAddressTest extends TestCase
{
    public function testImplementsSchemable(): void
    {
        $this->assertInstanceOf(Schemable::class, PostalAddress::from(['streetAddress' => 'Musterstraße 1']));
    }

    public function testEqualsIsTrueWhenAllStructuredFieldsMatch(): void
    {
        $address = new PostalAddress('Musterstraße 1', '12345', 'Berlin', 'DE', 'Berlin');
        $other = new PostalAddress('Musterstraße 1', '12345', 'Berlin', 'DE', 'Berlin');

        $this->assertTrue($address->equals($other));
    }

    public function testEqualsIsFalseWhenRegionDiffersEvenIfTheRenderedTextMatches(): void
    {
        $address = new PostalAddress('Musterstraße 1', '12345', 'Berlin', 'DE', 'Berlin');
        $other = new PostalAddress('Musterstraße 1', '12345', 'Berlin', 'DE', 'Brandenburg');

        $this->assertSame($address->toLocaleString(), $other->toLocaleString());
        $this->assertFalse($address->equals($other));
    }
}
