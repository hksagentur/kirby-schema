<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Arrayable;
use InvalidArgumentException;
use Kirby\Toolkit\Html;
use Kirby\Toolkit\Str;

readonly class PhoneNumber extends StringType implements Arrayable
{
    public const string INTERNATIONAL_PREFIX_SYMBOL = '+';

    public const string ITU_E123 = '/^(?<internationalPrefixSymbol>\+)?(?<countryCode>\d{1,3})?\s*(?<nationalSignificantNumber>(?<nationalTrunkCode>\(\d+\)|0\d+)?[\/\s-]*\s*(?<subscriberNumber>[\d\s-]*\d))$/u';
    public const string ITU_E164 = '/^(?<internationalPrefixSymbol>\+)?(?<countryCode>\d{1,3})(?<nationalSignificantNumber>\d{1,12})$/u';

    public string $nationalSignificantNumber;
    public ?string $subscriberNumber;
    public ?string $nationalTrunkCode;
    public ?string $countryCode;

    public function __construct(
        public string $value
    ) {
        if (! preg_match(self::ITU_E123, $value, $matches)) {
            throw new InvalidArgumentException(sprintf(
                'The given value "%s" does not represent a valid phone number.',
                $value,
            ));
        }

        $this->nationalSignificantNumber = $matches['nationalSignificantNumber'];

        $this->subscriberNumber = $matches['subscriberNumber'] ?? null;
        $this->countryCode = $matches['countryCode'] ?? null;

        $this->nationalTrunkCode = isset($matches['nationalTrunkCode'])
            ? preg_replace('/\D/', '', $matches['nationalTrunkCode'])
            : null;
    }

    public function isInternational(): bool
    {
        return $this->countryCode !== null;
    }

    public function isNational(): bool
    {
        return $this->countryCode === null;
    }

    public function hasCountryCode(): bool
    {
        return $this->countryCode !== null;
    }

    public function hasNationalSignificantNumber(): bool
    {
        return $this->nationalSignificantNumber !== null;
    }

    public function hasNationalTrunkCode(): bool
    {
        return $this->nationalTrunkCode !== null;
    }

    public function hasSubscriberNumber(): bool
    {
        return $this->subscriberNumber !== null;
    }

    public function countryCode(): ?string
    {
        return $this->countryCode;
    }

    public function countryCallingCode(): ?string
    {
        return $this->countryCode !== null ? self::INTERNATIONAL_PREFIX_SYMBOL . $this->countryCode : null;
    }

    public function nationalSignificantNumber(): string
    {
        return $this->nationalSignificantNumber;
    }

    public function nationalTrunkCode(): ?string
    {
        return $this->nationalTrunkCode;
    }

    public function subscriberNumber(): string
    {
        return $this->subscriberNumber;
    }

    public function obfuscate(): string
    {
        return Str::encode($this->value);
    }

    public function anonymize(int $length = 4, string $replacement = '…'): string
    {
        $significantNumber = $this->hasSubscriberNumber()
            ? $this->subscriberNumber()
            : $this->nationalSignificantNumber();

        $total = Str::length($significantNumber);

        if ($length >= $total) {
            $length = max(1, $total - 1);
        }

        $start = Str::substr($significantNumber, 0, (int) ceil($length / 2));
        $end = Str::substr($significantNumber, (int) floor($length / -2));

        $anonymizedSignificantNumber = $start . $replacement . $end;

        return implode(' ', array_filter([
            $this->countryCallingCode(),
            $this->nationalTrunkCode(),
            $anonymizedSignificantNumber,
        ]));
    }

    public function toE123String(): string
    {
        return implode(' ', array_filter([
            $this->countryCallingCode(),
            preg_replace('/[^\d\s\-]/', '', $this->nationalSignificantNumber),
        ]));
    }

    public function toE164String(): string
    {
        return implode('', [
            $this->countryCallingCode(),
            preg_replace('/[^\d]/', '', $this->nationalSignificantNumber),
        ]);
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'countryCode' => $this->countryCode,
            'nationalTrunkCode' => $this->nationalTrunkCode,
            'nationalSignificantNumber' => $this->nationalSignificantNumber,
            'subscriberNumber' => $this->subscriberNumber,
        ];
    }

    public function toHtml(array $attributes = []): string
    {
        return $this->toLink($attributes);
    }

    public function toLink(array $attributes = []): string
    {
        return Html::tel($this->value(), attr: $attributes);
    }
}
