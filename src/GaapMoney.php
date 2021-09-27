<?php

declare(strict_types=1);

namespace OnMoon\Money;

use Money\Currencies;
use Money\Currencies\ISOCurrencies;

class GaapMoney extends BaseMoney
{
    protected static function classSubunits(): int
    {
        return 4;
    }

    protected static function getAllowedCurrencies(): Currencies
    {
        return new ISOCurrencies();
    }
}
