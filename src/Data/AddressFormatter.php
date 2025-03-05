<?php

namespace Hks\Schema\Data;

use CommerceGuys\Addressing\Address;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepository;
use CommerceGuys\Addressing\Country\CountryRepository;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;
use Hks\Schema\Data\Address\MicroformatFormatter;
use Kirby\Cms\App;
use Kirby\Toolkit\A;

class AddressFormatter
{
    public function format(Address $address, array $options = []): string
    {
        $options = A::merge([
            'locale' => App::instance()->language()->locale(LC_ALL),
            'attributes' => [
                'translate' => 'no',
            ],
        ], $options);

        $addressFormatRepository = new AddressFormatRepository();
        $countryRepository = new CountryRepository($options['locale']);
        $subdivisonRepository = new SubdivisionRepository($addressFormatRepository);

        $formatter = new MicroformatFormatter($addressFormatRepository, $countryRepository, $subdivisonRepository, [
            'locale' => $options['locale'],
            'html' => true,
            'html_tag' => 'div',
            'html_attributes' => $options['attributes'] ?? [],
        ]);

        return $formatter->format($address);
    }
}
