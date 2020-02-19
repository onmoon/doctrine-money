<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests\Mocks;

use OnMoon\Money\Money;

class AmountMustBeGreaterThanZeroMoney extends Money
{
    protected static function amountMustBeGreaterThanZero() : bool
    {
        return true;
    }
}
