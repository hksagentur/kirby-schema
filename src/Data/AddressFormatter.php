<?php

namespace Hks\Schema\Data;

use CommerceGuys\Addressing\Address;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepository;
use CommerceGuys\Addressing\Country\CountryRepository;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;
use Hks\Schema\Data\Address\MicroformatFormatter;
use Kirby\Toolkit\A;

class AddressFormatter extends Formatter
{
    public function format(Address $address, array $options = []): string
    {
        $options = A::merge([
            'attributes' => [
                'translate' => 'no',
            ],
        ], $options);

        $locale = $options['locale'] ?? static::defaultLocale();

        $addressFormatRepository = new AddressFormatRepository();
        $subdivisonRepository = new SubdivisionRepository($addressFormatRepository);

        $countryRepository = new CountryRepository($locale);

        $formatter = new MicroformatFormatter($addressFormatRepository, $countryRepository, $subdivisonRepository, [
            'locale' => $locale,
            'html' => true,
            'html_tag' => 'div',
            'html_attributes' => $options['attributes'] ?? [],
        ]);

        return $formatter->format($address);
    }
}
