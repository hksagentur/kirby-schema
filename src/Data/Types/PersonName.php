<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Arrayable;
use InvalidArgumentException;
use Kirby\Toolkit\Str;
use Kirby\Toolkit\V;
use Stringable;
use Throwable;

readonly class PersonName extends DataType implements Arrayable
{
    public function __construct(
        public string $displayName,
        public ?string $givenName = null,
        public ?string $additionalName = null,
        public ?string $familyName = null,
        public ?string $honorificPrefix = null,
    ) {
        if (V::empty($displayName)) {
            throw new InvalidArgumentException(sprintf(
                'The given value "%s" does not represent a valid person name.',
                $displayName,
            ));
        }
    }

    public static function from(string|array|self|Stringable $value): static
    {
        if ($value instanceof self) {
            return $value;
        }

        if (is_array($value)) {
            return new static(
                displayName: $value['displayName'] ?? implode(' ', array_filter([
                    $value['honorificPrefix'] ?? null,
                    $value['givenName'] ?? null,
                    $value['additionalName'] ?? null,
                    $value['familyName'] ?? null,
                ])),
                givenName: $value['givenName'] ?? null,
                additionalName: $value['additionalName'] ?? null,
                familyName: $value['familyName'] ?? null,
                honorificPrefix: $value['honorificPrefix'] ?? null,
            );
        }

        return new static((string) $value);
    }

    public static function tryFrom(string|array|self|Stringable $value): ?static
    {
        try {
            return static::from($value);
        } catch (Throwable) {
            return null;
        }
    }

    public function displayName(): string
    {
        return $this->displayName;
    }

    public function givenName(): ?string
    {
        return $this->givenName;
    }

    public function additionalName(): ?string
    {
        return $this->additionalName;
    }

    public function familyName(): ?string
    {
        return $this->familyName;
    }

    public function honorificPrefix(): ?string
    {
        return $this->honorificPrefix;
    }

    /** @see self::givenName() */
    public function firstName(): ?string
    {
        return $this->givenName();
    }

    /** @see self::additionalName() */
    public function middleName(): ?string
    {
        return $this->additionalName();
    }

    /** @see self::familyName() */
    public function lastName(): ?string
    {
        return $this->familyName();
    }

    public function sortKey(): string
    {
        return Str::slug($this->familyName ?? $this->displayName);
    }

    public function toArray(): array
    {
        return [
            'displayName' => $this->displayName,
            'givenName' => $this->givenName,
            'additionalName' => $this->additionalName,
            'familyName' => $this->familyName,
            'honorificPrefix' => $this->honorificPrefix,
        ];
    }

    public function toString(): string
    {
        return $this->displayName();
    }
}
