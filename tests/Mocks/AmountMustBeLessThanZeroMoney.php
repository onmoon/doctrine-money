<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests\Mocks;

use OnMoon\Money\Money;

class AmountMustBeLessThanZeroMoney extends Money
{
    protected static function amountMustBeLessThanZero() : bool
    {
        return true;
    }
}
