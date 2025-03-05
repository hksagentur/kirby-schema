<?php

namespace Hks\Schema\Data\Address;

use CommerceGuys\Addressing\AddressFormat\AddressField;
use CommerceGuys\Addressing\AddressFormat\AddressFormat;
use CommerceGuys\Addressing\AddressInterface;
use CommerceGuys\Addressing\Formatter\DefaultFormatter;
use Locale;

class MicroformatFormatter extends DefaultFormatter
{
    protected function buildView(AddressInterface $address, AddressFormat $addressFormat, array $options): array
    {
        $localeRegion = Locale::getRegion($options['locale']);

        $countries = $this->countryRepository->getList($options['locale']);
        $countryCode = $address->getCountryCode();

        $view = [
            'country' => [
                'html' => $options['html'],
                'html_tag' => 'span',
                'html_attributes' => [
                    'class' => array_filter([
                        'p-country-name',
                        'country-name',
                        $countryCode === $localeRegion ? 'visually-hidden' : null,
                    ]),
                ],
                'value' => $countries[$countryCode] ?? $countryCode,
            ]
        ];

        $values = $this->getValues($address, $addressFormat);

        foreach ($addressFormat->getUsedFields() as $field) {
            $view[$field] = [
                'html' => $options['html'],
                'html_tag' => 'span',
                'html_attributes' => [
                    'class' => $this->getClassNameForField($field),
                ],
                'value' => $values[$field],
            ];
        }

        return $view;
    }

    protected function getClassNameForField(string $field): string|array
    {
        return match ($field) {
            AddressField::ADMINISTRATIVE_AREA => ['p-region', 'region'],
            AddressField::LOCALITY => ['p-locality', 'locality'],
            AddressField::POSTAL_CODE => ['p-postal-code', 'postal-code'],
            AddressField::ADDRESS_LINE1 => ['p-street-address', 'street-address'],
            AddressField::ADDRESS_LINE2 => ['p-extended-address', 'extended-address'],
            AddressField::ORGANIZATION => ['p-org', 'org'],
            AddressField::GIVEN_NAME => ['p-given-name', 'given-name'],
            AddressField::ADDITIONAL_NAME => ['p-additional-name', 'additinal-name'],
            AddressField::FAMILY_NAME => ['p-family-name', 'family-name'],
            default => str_replace('_', '-', strtolower(AddressField::getKey($field))),
        };
    }
}
