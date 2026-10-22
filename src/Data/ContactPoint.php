<?php

namespace Hks\Schema\Data;

use Hks\Schema\Data\Types\EmailAddress;
use Hks\Schema\Data\Types\PhoneNumber;
use Kirby\Cms\App;
use Kirby\Toolkit\Str;

readonly class ContactPoint extends DataObject
{
    public function __construct(
        public string $type,
        public ?PhoneNumber $telephone = null,
        public ?EmailAddress $email = null,
    ) {
    }

    public static function fromArray(array $value): static
    {
        $value = array_change_key_case($value);

        return new static(
            type: $value['contacttype'] ?? '',
            telephone: isset($value['telephone']) ? PhoneNumber::tryFrom($value['telephone']) : null,
            email: isset($value['email']) ? EmailAddress::tryFrom($value['email']) : null,
        );
    }

    public function hasType(string $contactType): bool
    {
        return Str::lower($this->type) === Str::lower($contactType);
    }

    public function type(): string
    {
        return $this->type;
    }

    public function telephone(): ?PhoneNumber
    {
        return $this->telephone;
    }

    public function email(): ?EmailAddress
    {
        return $this->email;
    }

    public function toHtml(array $attributes = []): string
    {
        return App::instance()->snippet('schema/contact-point', [
            'item' => $this,
            'attrs' => $attributes,
        ]);
    }

    public function toArray(): array
    {
        return [
            'contactType' => $this->type,
            'telephone' => $this->telephone?->toString(),
            'email' => $this->email?->toString(),
        ];
    }

    public function toStructuredData(string $type = 'ContactPoint'): array
    {
        return array_filter([
            '@type' => $type,
            'contactType' => $this->type,
            'telephone' => $this->telephone?->value,
            'email' => $this->email?->value,
        ]);
    }
}
