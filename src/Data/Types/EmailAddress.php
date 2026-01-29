<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Arrayable;
use InvalidArgumentException;
use Kirby\Http\Idn;
use Kirby\Toolkit\V;
use Kirby\Toolkit\Html;
use Kirby\Toolkit\Str;

readonly class EmailAddress extends StringType implements Arrayable
{
    public const string SEPARATOR = '@';

    public string $localPart;
    public string $domain;

    public function __construct(
        public string $value
    ) {
        if (! V::email($value)) {
            throw new InvalidArgumentException(sprintf(
                'The given value "%s" does not represent a valid email address.',
                $value,
            ));
        }

        [$this->localPart, $this->domain] = Str::split($value, self::SEPARATOR);
    }

    public function localPart(): string
    {
        return $this->localPart;
    }

    public function domain(): string
    {
        return $this->domain;
    }

    public function with(array $parts): static
    {
        $parts = array_merge($this->toArray(), $parts);

        return new static(implode('', [
            $parts['localPart'],
            self::SEPARATOR,
            $parts['domain'],
        ]));
    }

    public function obfuscate(): string
    {
        return Str::encode($this->value);
    }

    public function anonymize(int $length = 4, string $replacement = '…'): string
    {
        $total = Str::length($this->localPart);

        if ($length >= $total) {
            $length = max(1, $total - 1);
        }

        $start = Str::substr($this->localPart, 0, (int) ceil($length / 2));
        $end = Str::substr($this->localPart, (int) floor($length / -2));

        return $start . $replacement . $end . self::SEPARATOR . $this->domain;
    }

    public function toPunycode(): static
    {
        return new static(Idn::encodeEmail($this->value));
    }

    public function toUnicode(): static
    {
        return new static(Idn::decodeEmail($this->value));
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'localPart' => $this->localPart,
            'domain' => $this->domain,
        ];
    }

    public function toHtml(array $attributes = []): string
    {
        return $this->toLink($attributes);
    }

    public function toLink(array $attributes = []): string
    {
        return Html::email($this->value, attr: $attributes);
    }
}
