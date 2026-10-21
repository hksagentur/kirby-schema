<?php

namespace Hks\Schema\Data\Types;

use Hks\Schema\Data\Contracts\Localizable;
use Hks\Schema\Data\Exceptions\NotFoundException;
use InvalidArgumentException;
use Kirby\Cms\App;
use Kirby\Toolkit\I18n;
use Kirby\Toolkit\Str;
use NumberFormatter;

readonly class Currency extends StringType implements Localizable
{
    public const string ISO_CURRENCY_CODE = '/^[A-Z]{3}$/';

    public function __construct(string $code)
    {
        $code = Str::upper($code);

        if (! Str::matches($code, self::ISO_CURRENCY_CODE)) {
            throw new InvalidArgumentException(sprintf(
                'The given value "%s" does not represent an ISO 4217 currency code.',
                $code,
            ));
        }

        parent::__construct($code);
    }

    public static function default(): static
    {
        $currency = static::tryFrom(
            (string) App::instance()->option('hksagentur.schema.defaultCurrency')
        );

        if (! $currency) {
            throw NotFoundException::for(
                'The default currency "%s" is not supported.',
                (string) App::instance()->option('hksagentur.schema.defaultCurrency'),
            );
        }

        return $currency;
    }

    public function code(): string
    {
        return $this->value;
    }

    public function symbol(?string $locale = null): string
    {
        return $this->formatter($locale)->getSymbol(NumberFormatter::CURRENCY_SYMBOL);
    }

    public function minorUnits(): int
    {
        return $this->formatter('en')->getAttribute(NumberFormatter::FRACTION_DIGITS);
    }

    public function toLocaleString(?string $locale = null, array $options = []): string
    {
        return I18n::translate("hksagentur.schema.currency.{$this->value}", $this->value, $locale);
    }

    protected function formatter(?string $locale): NumberFormatter
    {
        $locale ??= I18n::locale();

        return new NumberFormatter("{$locale}@currency={$this->value}", NumberFormatter::CURRENCY);
    }
}
