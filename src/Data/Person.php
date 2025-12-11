<?php

namespace Hks\Schema\Data;

use Hks\Schema\Formatter\PersonHtmlFormatter;
use Hks\Schema\Formatter\PersonSchemaTransformer;
use Hks\Schema\Formatter\PersonTextFormatter;
use InvalidArgumentException;

readonly class Person extends DataObject
{
    public function __construct(
        public ?Name $givenName = null,
        public ?Name $additionalName = null,
        public ?Name $familyName = null,
        public ?ImageUrl $image = null,
        public ?Markdown $description = null,
        public ?Email $email = null,
        public ?PhoneNumber $telephone = null,
        public ?PhoneNumber $fax = null,
        /** @param ?DataCollection<Role> */
        public ?DataCollection $roles = null,
    ) {
        if ($givenName->isEmpty() && $familyName->isEmpty()) {
            throw new InvalidArgumentException(
                'Person requires at least a givenName or familyName, but both are missing.'
            );
        }
    }

    public static function fromArray(array $attributes): static
    {
        return new static(
            givenName: Name::tryFrom($attributes['givenName']),
            additionalName: Name::tryFrom($attributes['additionalName'] ?? null),
            familyName: Name::tryFrom($attributes['familyName'] ?? null),
            image: ImageUrl::tryFrom($attributes['image'] ?? null),
            description: Markdown::tryFrom($attributes['description'] ?? null),
            email: Email::tryFrom($attributes['email'] ?? null),
            telephone: PhoneNumber::tryFrom($attributes['telephone'] ?? null),
            fax: PhoneNumber::tryFrom($attributes['fax'] ?? null),
            roles: DataCollection::from(array_map(Role::tryFromArray(...), $attributes['roles'] ?? [])),
        );
    }

    public function fullName(): Name
    {
        return Name::from(implode(' ', array_filter([
            $this->givenName?->toString(),
            $this->additionalName?->toString(),
            $this->familyName?->toString(),
        ])));
    }

    public function givenName(): Name
    {
        return $this->givenName;
    }

    public function additionalName(): ?Name
    {
        return $this->additionalName;
    }

    public function familyName(): ?Name
    {
        return $this->familyName;
    }

    public function image(): ?ImageUrl
    {
        return $this->image;
    }

    public function description(): ?Markdown
    {
        return $this->description;
    }

    public function email(): ?Email
    {
        return $this->email;
    }

    public function telephone(): ?PhoneNumber
    {
        return $this->telephone;
    }

    public function fax(): ?PhoneNumber
    {
        return $this->fax;
    }

    /** @return DataCollection<Role> */
    public function roles(): DataCollection
    {
        return $this->roles ?? DataCollection::empty();
    }

    /** @return DataCollection<Role> */
    public function responsibilities(): DataCollection
    {
        $responsibilities = [];

        foreach ($this->roles() as $role) {
            foreach ($role->responsibilities() as $responsibility) {
                $responsibilities[] = $responsibility;
            }
        }

        return new DataCollection($responsibilities);
    }

    public function toArray(): array
    {
        return [
            'givenName' => $this->givenName?->toString(),
            'additionalName' => $this->additionalName?->toString(),
            'familyName' => $this->familyName?->toString(),
            'image' => $this->image?->toString(),
            'description' => $this->description?->toString(),
            'email' => $this->email?->toString(),
            'telephone' => $this->telephone?->toString(),
            'fax' => $this->fax?->toString(),
            'roles' => $this->roles?->toArray(),
        ];
    }

    public function toString(): string
    {
        return $this->format(PersonTextFormatter::class);
    }

    public function toHtml(array $attributes = []): string
    {
        return $this->format(PersonHtmlFormatter::class, ['attributes' => $attributes]);
    }

    public function toSchema(): array
    {
        return $this->transform(PersonSchemaTransformer::class);
    }
}
