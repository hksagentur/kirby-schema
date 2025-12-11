<?php

use Hks\Schema\Data\Factory\GeoCoordinateFactory;
use Hks\Schema\Data\Factory\OpeningHoursFactory;
use Hks\Schema\Data\Factory\OrganizationFactory;
use Hks\Schema\Data\Factory\PersonFactory;
use Hks\Schema\Data\Factory\PostalAddressFactory;
use Hks\Schema\Data\GeoCoordinate;
use Hks\Schema\Data\Organization;
use Hks\Schema\Data\Person;
use Hks\Schema\Data\PostalAddress;
use Spatie\OpeningHours\OpeningHours;

return [

    // Converters

    /**
     * Converts the content of the current structure object to a geo coordinate object.
     */
    'toGeoCoordinate' => function (): GeoCoordinate {
        return GeoCoordinateFactory::createFromContent($this->content());
    },

    /**
     * Converts the content of the current structure object to an opening hours object.
     */
    'toOpeningHours' => function (): OpeningHours {
        return OpeningHoursFactory::createFromContent($this->content());
    },

    /**
     * Converts the content of the current structure object to an organization object.
     */
    'toOrganization' => function (): Organization {
        return OrganizationFactory::createFromContent($this->content());
    },

    /**
     * Converts the content of the current structure object to a person object.
     */
    'toPerson' => function (): Person {
        return PersonFactory::createFromContent($this->content());
    },

    /**
     * Converts the content of the current structure object to a postal address object.
     */
    'toPostalAddress' => function (): PostalAddress {
        return PostalAddressFactory::createFromContent($this->content());
    },

];
