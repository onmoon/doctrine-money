<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests\Mocks;

use Money\Currencies;
use Money\Currencies\CurrencyList;
use OnMoon\Money\Money;

class InvalidSubunitCurrencyMoney extends Money
{
    protected static function subUnits(): int
    {
        return 2;
    }

    protected static function getAllowedCurrencies(): Currencies
    {
        return new CurrencyList(['OMR' => 3]);
    }
}
