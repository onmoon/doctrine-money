<?php

declare(strict_types=1);

namespace OnMoon\Money;

class Money extends BaseMoney
{
    protected static function subUnits() : int
    {
        return 2;
    }
}
