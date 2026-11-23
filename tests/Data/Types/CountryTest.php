<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\TestCase;
use InvalidArgumentException;
use Kirby\Cms\Page;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;
use Kirby\Option\OptionsQuery;

class CountryTest extends TestCase
{
    public function testAcceptsAnIsoCountryCodeCaseInsensitively(): void
    {
        $this->assertSame('AT', Country::from('at')->code());
    }

    public function testRejectsAValueThatIsNoCountryCode(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Country::from('Deutschland');
    }

    public function testTryFromReturnsNullForAValueThatIsNoCountryCode(): void
    {
        $this->assertNull(Country::tryFrom('Deutschland'));
        $this->assertNull(Country::tryFrom(''));
    }

    public function testToLocaleStringReturnsTheTranslatedName(): void
    {
        $this->assertSame('Österreich', Country::from('AT')->toLocaleString('de'));
        $this->assertSame('Austria', Country::from('AT')->toLocaleString('en'));
    }

    public function testToLocaleStringFallsBackToTheCodeWithoutATranslation(): void
    {
        $this->assertSame('JP', Country::from('JP')->toLocaleString('de'));
    }

    public function testDefaultIsTheConfiguredCountry(): void
    {
        $this->assertSame('DE', Country::default()->code());
    }

    public function testAllListsTheCountriesWithANameSortedByName(): void
    {
        $countries = Country::all('de');

        $this->assertSame('Australien', $countries->first()->toLocaleString('de'));
        $this->assertSame('Zypern', $countries->last()->toLocaleString('de'));
        $this->assertSame('DE', $countries->get('DE')->code());
        $this->assertNull($countries->get('JP'));
    }

    public function testAProjectCollectionExtendsTheCountries(): void
    {
        $root = sys_get_temp_dir() . '/kirby-schema-collections';
        F::write($root . '/schema/countries.php', <<<'PHP'
            <?php

            use Hks\Schema\Data\Types\Country;

            return fn () => Country::all()->append('JP', Country::from('JP'));
            PHP);

        $app = $this->app->clone(['roots' => ['collections' => $root]]);

        $this->assertSame('JP', $app->collection('schema/countries')->get('JP')?->code());
        $this->assertSame('DE', $app->collection('schema/countries')->get('DE')?->code());

        Dir::remove($root);
    }

    public function testTheCollectionOffersTheCountriesToTheAddressField(): void
    {
        $countries = $this->app->collection('schema/countries');

        $this->assertSame('DE', $countries->get('DE')?->code());
    }

    public function testTheAddressFieldResolvesTheCollectionToOptions(): void
    {
        $options = OptionsQuery::factory([
            'query' => 'kirby.collection("schema/countries")',
            'text' => '{{ item.toLocaleString }}',
        ])->resolve(new Page(['slug' => 'test']))->render(new Page(['slug' => 'test']));

        $this->assertContains(['disabled' => false, 'icon' => null, 'info' => null, 'text' => 'Germany', 'value' => 'DE'], $options);
    }
}
