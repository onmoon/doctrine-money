<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests\Mocks;

use OnMoon\Money\Money;

class AmountMustBeZeroOrGreaterMoney extends Money
{
    protected static function amountMustBeZeroOrGreater() : bool
    {
        return true;
    }
}
