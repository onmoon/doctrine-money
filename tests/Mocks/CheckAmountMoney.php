<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests\Mocks;

use OnMoon\Money\BaseMoney;
use OnMoon\Money\Currency;
use OnMoon\Money\Exception\CannotCreateMoney;
use OnMoon\Money\Money;

class CheckAmountMoney extends Money
{
    protected static function validate(BaseMoney $money): void
    {
        if ($money->lessThanOrEqual(Money::create('100.00', Currency::create('EUR')))) {
            return;
        }

        throw new CannotCreateMoney('Money amount is greater than 100.00 EUR');
    }
}
