<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Arrayable;
use Hks\Schema\Toolkit\CallingCode;
use InvalidArgumentException;
use Kirby\Toolkit\Str;

readonly class PhoneNumber extends StringType implements Arrayable
{
    /**
     * Matches a calling code.
     *
     * Calling codes are prefix-free, so their first digits decide how many digits belong to them.
     *
     * @link https://www.itu.int/rec/T-REC-E.164
     */
    public const string CALLING_CODE = '[17]|2[07]|3[0-469]|4[013-9]|5[1-8]|6[0-6]|8[1246]|9[0-58]|[2-9]\d{2}';

    public const string ITU_E123 = '/^(?:(?<internationalPrefix>\\' . self::INTERNATIONAL_PREFIX_SYMBOL . '|' . self::INTERNATIONAL_PREFIX . ')(?<callingCode>' . self::CALLING_CODE . '))?\s*(?<nationalNumber>(?<nationalTrunkCode>\(\d+\)|0\d+)?[\/\s-]*\s*(?<subscriberNumber>[\d\s-]*\d))$/u';
    public const string ITU_E164 = '/^(?<internationalPrefix>\\' . self::INTERNATIONAL_PREFIX_SYMBOL . ')?(?<callingCode>' . self::CALLING_CODE . ')(?<nationalSignificantNumber>\d{1,12})$/u';

    public const string INTERNATIONAL_PREFIX = '00';
    public const string INTERNATIONAL_PREFIX_SYMBOL = '+';

    public string $nationalNumber;
    public ?string $subscriberNumber;
    public ?string $nationalTrunkCode;
    public ?string $callingCode;

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

        $this->nationalNumber = $matches['nationalNumber'];

        $this->subscriberNumber = $matches['subscriberNumber'] ?? null;
        $this->callingCode = $matches['callingCode'] ?? null;

        $this->nationalTrunkCode = isset($matches['nationalTrunkCode'])
            ? $this->digits($matches['nationalTrunkCode'])
            : null;
    }

    public function isInternational(): bool
    {
        return $this->callingCode !== null;
    }

    public function isNational(): bool
    {
        return $this->callingCode === null;
    }

    public function hasCallingCode(): bool
    {
        return $this->callingCode !== null;
    }

    public function hasNationalTrunkCode(): bool
    {
        return $this->nationalTrunkCode !== null;
    }

    public function hasSubscriberNumber(): bool
    {
        return $this->subscriberNumber !== null;
    }

    public function callingCode(): ?string
    {
        return $this->callingCode;
    }

    public function nationalNumber(): string
    {
        return $this->nationalNumber;
    }

    public function nationalSignificantNumber(?string $countryCode = null): string
    {
        return $this->significantNumber($this->callingCodeFor($countryCode));
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
            : $this->nationalNumber();

        $total = Str::length($significantNumber);

        if ($length >= $total) {
            $length = max(1, $total - 1);
        }

        $start = Str::substr($significantNumber, 0, (int) ceil($length / 2));
        $end = Str::substr($significantNumber, (int) floor($length / -2));

        $anonymizedSignificantNumber = $start . $replacement . $end;

        return implode(' ', array_filter([
            $this->isInternational() ? self::INTERNATIONAL_PREFIX_SYMBOL . $this->callingCode : null,
            $this->nationalTrunkCode(),
            $anonymizedSignificantNumber,
        ]));
    }

    public function toE123String(): string
    {
        if (! $this->isInternational()) {
            return $this->groupedDigits($this->nationalNumber);
        }

        return self::INTERNATIONAL_PREFIX_SYMBOL . $this->callingCode . ' ' . $this->groupedDigits($this->significantNumber($this->callingCodeFor()));
    }

    public function toE164String(?string $countryCode = null): string
    {
        $callingCode = $this->callingCodeFor($countryCode);

        if ($callingCode === null) {
            return $this->digits($this->nationalNumber);
        }

        return self::INTERNATIONAL_PREFIX_SYMBOL . $callingCode->code() . $this->digits($this->significantNumber($callingCode));
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'callingCode' => $this->callingCode,
            'nationalTrunkCode' => $this->nationalTrunkCode,
            'nationalNumber' => $this->nationalNumber,
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

    protected function callingCodeFor(?string $countryCode = null): ?CallingCode
    {
        if ($this->isInternational()) {
            return CallingCode::find($this->callingCode) ?? new CallingCode($this->callingCode);
        }

        $country = $countryCode === null ? Country::default() : Country::tryFrom($countryCode);

        return $country ? CallingCode::for($country) : null;
    }

    protected function digits(string $number): string
    {
        return preg_replace('/\D/', '', $number);
    }

    protected function groupedDigits(string $number): string
    {
        return preg_replace('/[^\d\s\-]/', '', $number);
    }

    protected function significantNumber(?CallingCode $callingCode): string
    {
        $number = trim($this->nationalNumber);

        if ($callingCode?->nationalPrefix() === null) {
            return $number;
        }

        return preg_replace('/^\(?' . preg_quote($callingCode->nationalPrefix(), '/') . '\)?\s*/', '', $number);
    }
}
