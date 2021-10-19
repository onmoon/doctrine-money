<?php

declare(strict_types=1);

namespace OnMoon\Money;

use Money\Currencies;
use Money\Currencies\BitcoinCurrencies;

class Bitcoin extends BaseMoney
{
    public static function humanReadableName(): string
    {
        return 'Bitcoin';
    }

    protected static function classSubunits(): int
    {
        return 8;
    }

    protected static function getAllowedCurrencies(): Currencies
    {
        return new BitcoinCurrencies();
    }
}
