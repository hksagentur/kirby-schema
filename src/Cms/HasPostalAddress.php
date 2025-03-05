<?php

namespace Hks\Schema\Cms;

use CommerceGuys\Addressing\Address;
use Hks\Schema\Data\AddressFactory;

trait HasPostalAddress
{
    protected ?Address $postalAddress = null;

    public function postalAddress(): Address
    {
        return $this->postalAddress ??= AddressFactory::createFromContent($this->content());
    }
}
