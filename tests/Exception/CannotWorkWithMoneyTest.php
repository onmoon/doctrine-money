<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests\Exception;

use OnMoon\Money\Exception\CannotWorkWithMoney;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class CannotWorkWithMoneyTest extends TestCase
{
    public function testBecauseBothCurrenciesSame() : void
    {
        $method        = 'add';
        $class         = 'OnMoon\Money\Money';
        $otherClass    = 'OnMoon\Money\GaapMoney';
        $subunits      = 2;
        $otherSubunits = 4;

        $exception = CannotWorkWithMoney::becauseMoneyHasDifferentSubunit(
            $method,
            $class,
            $otherClass,
            $subunits,
            $otherSubunits
        );

        Assert::assertSame(
            'Cannot execute method: add on Money object: OnMoon\Money\Money with other Money object as argument: OnMoon\Money\GaapMoney. The classes have different subunits: 2 and 4.',
            $exception->getMessage()
        );
    }
}
