<?php

declare(strict_types=1);

namespace OnMoon\Money;

use OnMoon\Money\Exception\CannotCreateMoney;

class BTC extends BaseMoney
{
    protected static function subUnits() : int
    {
        return 8;
    }

    protected static function validate(BaseMoney $money) : void
    {
        if ($money->getCurrency()->getCode() === 'XBT') {
            return;
        }

        throw CannotCreateMoney::becauseCurrencyMustBeBTC($money->getCurrency()->getCode());
    }
}
