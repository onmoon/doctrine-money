<?php

declare(strict_types=1);

namespace OnMoon\Money;

class GaapMoney extends BaseMoney
{
    protected static function subUnits() : int
    {
        return 4;
    }
}
