<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests\Mocks;

use OnMoon\Money\Money;

class AmountMustBeZeroOrLessMoney extends Money
{
    protected static function amountMustBeZeroOrLess(): bool
    {
        return true;
    }
}
