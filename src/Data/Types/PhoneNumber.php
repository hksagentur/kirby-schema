<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Arrayable;
use Hks\Schema\Format\PhoneNumberFormat;
use InvalidArgumentException;
use Kirby\Toolkit\Str;

readonly class PhoneNumber extends StringType implements Arrayable
{
    /**
     * Matches a country calling code.
     *
     * Calling codes are prefix-free, so their first digits decide how many digits belong to them.
     *
     * @link https://www.itu.int/rec/T-REC-E.164
     */
    public const string COUNTRY_CODE = '[17]|2[07]|3[0-469]|4[013-9]|5[1-8]|6[0-6]|8[1246]|9[0-58]|[2-9]\d{2}';

    public const string ITU_E123 = '/^(?:(?<internationalPrefix>\\' . self::INTERNATIONAL_PREFIX_SYMBOL . '|' . self::INTERNATIONAL_PREFIX . ')(?<countryCode>' . self::COUNTRY_CODE . '))?\s*(?<nationalSignificantNumber>(?<nationalTrunkCode>\(\d+\)|0\d+)?[\/\s-]*\s*(?<subscriberNumber>[\d\s-]*\d))$/u';
    public const string ITU_E164 = '/^(?<internationalPrefix>\\' . self::INTERNATIONAL_PREFIX_SYMBOL . ')?(?<countryCode>' . self::COUNTRY_CODE . ')(?<nationalSignificantNumber>\d{1,12})$/u';

    public const string INTERNATIONAL_PREFIX = '00';
    public const string INTERNATIONAL_PREFIX_SYMBOL = '+';

    public string $nationalSignificantNumber;
    public ?string $subscriberNumber;
    public ?string $nationalTrunkCode;
    public ?string $countryCode;

    public function __construct(string $value)
    {
        parent::__construct($value);

        if (! preg_match(self::ITU_E123, $value, $matches)) {
            throw new InvalidArgumentException(sprintf(
                'The given value "%s" does not represent a valid phone number.',
                $value,
            ));
        }

        $matches = array_filter($matches, static fn (string $match): bool => $match !== '');

        $this->nationalSignificantNumber = $matches['nationalSignificantNumber'];

        $this->subscriberNumber = $matches['subscriberNumber'] ?? null;
        $this->countryCode = $matches['countryCode'] ?? null;

        $this->nationalTrunkCode = isset($matches['nationalTrunkCode'])
            ? $this->digits($matches['nationalTrunkCode'])
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
        if (! $this->isInternational()) {
            return $this->groupedDigits($this->nationalSignificantNumber);
        }

        return $this->countryCallingCode() . ' ' . $this->groupedDigits($this->significantNumber($this->numberFormat()));
    }

    public function toE164String(?string $countryCode = null): string
    {
        $format = $this->numberFormat($countryCode);
        $callingCode = $this->countryCode() ?? $format?->callingCode();

        if ($callingCode === null) {
            return $this->digits($this->nationalSignificantNumber);
        }

        return self::INTERNATIONAL_PREFIX_SYMBOL . $callingCode . $this->digits($this->significantNumber($format));
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

    public function toUri(?string $countryCode = null): string
    {
        return 'tel:' . $this->toE164String($countryCode);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    protected function numberFormat(?string $countryCode = null): ?PhoneNumberFormat
    {
        return $this->isInternational()
            ? PhoneNumberFormat::byCallingCode($this->countryCode)
            : PhoneNumberFormat::get($countryCode);
    }

    protected function digits(string $number): string
    {
        return preg_replace('/\D/', '', $number);
    }

    protected function groupedDigits(string $number): string
    {
        return preg_replace('/[^\d\s\-]/', '', $number);
    }

    protected function significantNumber(?PhoneNumberFormat $format): string
    {
        $number = trim($this->nationalSignificantNumber);

        if ($format?->trunkPrefix() === null) {
            return $number;
        }

        return preg_replace('/^\(?' . preg_quote($format->trunkPrefix(), '/') . '\)?\s*/', '', $number);
    }
}
