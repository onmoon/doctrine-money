<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests\Mocks;

use OnMoon\Money\Money;

class ZeroSubunitMoney extends Money
{
    protected static function subUnits() : int
    {
        return 0;
    }
}
