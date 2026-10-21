<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Htmlable;
use Hks\Schema\Data\Contracts\Schemable;
use Hks\Schema\Data\Exceptions\NotFoundException;
use Hks\Schema\TestCase;
use Kirby\Cms\App;

class PostalAddressTest extends TestCase
{
    public function testImplementsSchemable(): void
    {
        $this->assertInstanceOf(Schemable::class, PostalAddress::from(['streetAddress' => 'Musterstraße 1']));
    }

    public function testImplementsHtmlable(): void
    {
        $this->assertInstanceOf(Htmlable::class, PostalAddress::from(['streetAddress' => 'Musterstraße 1']));
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

    public function testJsonSerializeMatchesToArray(): void
    {
        $address = new PostalAddress('Musterstraße 1', '12345', 'Berlin', 'DE', 'Berlin');

        $this->assertSame($address->toArray(), $address->jsonSerialize());
        $this->assertSame(
            $address->toArray(),
            json_decode(json_encode($address), true),
        );
    }

    public function testToStructuredDataOmitsEmptyParts(): void
    {
        $address = PostalAddress::from(['streetAddress' => 'Main Street 1', 'addressLocality' => 'Achim']);

        $this->assertSame([
            '@type' => 'PostalAddress',
            'streetAddress' => 'Main Street 1',
            'addressLocality' => 'Achim',
        ], $address->toStructuredData());
    }

    public function testAddressLinesFollowTheFormatOfTheAddressCountry(): void
    {
        $address = PostalAddress::from([
            'streetAddress' => 'Váci utca 1',
            'postalCode' => '1052',
            'addressLocality' => 'Budapest',
            'addressCountry' => 'HU',
        ]);

        $this->assertSame(['Budapest', 'Váci utca 1', '1052'], $address->addressLines(fn (string $value) => $value)->slice(0, 3)->values());
    }

    public function testAddressLinesDropASeparatorLeftByAnEmptyRegion(): void
    {
        $address = PostalAddress::from([
            'streetAddress' => 'İstiklal Cd. 1',
            'postalCode' => '34433',
            'addressLocality' => 'Beyoğlu',
            'addressCountry' => 'TR',
        ]);

        $this->assertSame('34433 Beyoğlu', $address->addressLines(fn (string $value) => $value)->nth(1));
    }

    public function testAddressLinesCollapseTheGapLeftByAnEmptyRegion(): void
    {
        $address = PostalAddress::from([
            'streetAddress' => '742 Evergreen Terrace',
            'postalCode' => '12345',
            'addressLocality' => 'Springfield',
            'addressCountry' => 'US',
        ]);

        $this->assertSame('Springfield, 12345', $address->addressLines(fn (string $value) => $value)->nth(1));
    }

    public function testAddressLinesThrowWithoutAnAddressFormat(): void
    {
        $this->app = new App([
            'roots' => ['index' => '/dev/null'],
            'options' => ['hksagentur.schema.defaultCountry' => 'XX'],
        ]);

        $this->expectException(NotFoundException::class);

        PostalAddress::from(['addressLocality' => 'Springfield', 'addressCountry' => 'XX'])->addressLines(fn (string $value) => $value);
    }
}
