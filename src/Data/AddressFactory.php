<?php

namespace Hks\Schema\Data;

use CommerceGuys\Addressing\Address;
use Hks\Schema\Toolkit\Country;
use Kirby\Content\Content;

class AddressFactory
{
    public const DEFAULT_COUNTRY = 'DE';

    public static function createFromArray(array $data): Address
    {
        return new Address(
            addressLine1: $data['address_line_1'] ?? '',
            addressLine2: $data['address_line_2'] ?? '',
            addressLine3: $data['address_line_3'] ?? '',
            postalCode: $data['postal_code'] ?? '',
            locality: $data['locality'] ?? '',
            countryCode: $data['country_code'] ?? self::DEFAULT_COUNTRY,
        );
    }

    public static function createFromContent(Content $content): Address
    {
        return static::createFromArray([
            'address_line_1' => $content->streetAddress()->value(),
            'postal_code' => $content->postalCode()->value(),
            'locality' => $content->locality()->value(),
            'country_code' => match (true) {
                $content->country()->exists() => Country::codeFromName($content->country()->value()),
                $content->countryCode()->exists() => $content->countryCode()->value(),
                default => null,
            },
        ]);
    }
}
