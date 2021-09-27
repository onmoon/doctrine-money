<?php

declare(strict_types=1);

namespace OnMoon\Money;

use Money\Currencies;
use Money\Currencies\CurrencyList;
use Money\Currencies\ISOCurrencies;

class Money extends BaseMoney
{
    private static ?Currencies $currencies = null;

    protected static function classSubunits(): int
    {
        return 2;
    }

    protected static function getAllowedCurrencies(): Currencies
    {
        if (self::$currencies !== null) {
            return self::$currencies;
        }

        return self::initializeCurrencies();
    }

    private static function initializeCurrencies(): Currencies
    {
        $isoCurrencies              = new ISOCurrencies();
        $twoOrLessSubUnitCurrencies = [];

        foreach ($isoCurrencies->getIterator() as $currency) {
            $subUnit = $isoCurrencies->subunitFor($currency);

            if ($subUnit > 2) {
                continue;
            }

            $twoOrLessSubUnitCurrencies[$currency->getCode()] = $subUnit;
        }

        /** @psalm-var array<non-empty-string, positive-int|0> $twoOrLessSubUnitCurrencies */
        $currencies = new CurrencyList($twoOrLessSubUnitCurrencies);

        self::$currencies = $currencies;

        return $currencies;
    }
}
