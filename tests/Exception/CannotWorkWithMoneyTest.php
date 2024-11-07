<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests\Exception;

use OnMoon\Money\Exception\CannotWorkWithMoney;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class CannotWorkWithMoneyTest extends TestCase
{
    public function testBecauseCurrencyExceedsSubunitLimit(): void
    {
        $name             = 'OnMoon\Money\Money';
        $amount           = '10.000';
        $currency         = 'BHD';
        $currencySubunits = 3;
        $classSubunits    = 2;

        $exception = CannotWorkWithMoney::becauseCurrencyExceedsSubunitLimit(
            $name,
            $amount,
            $currency,
            $currencySubunits,
            $classSubunits,
        );

        Assert::assertSame(
            'Cannot instantiate OnMoon\Money\Money with amount: 10.000 and currency: BHD. The currency has more subunits: 3 then the class allows: 2.',
            $exception->getMessage(),
        );
    }

    public function testBecauseBothCurrenciesSame(): void
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
            $otherSubunits,
        );

        Assert::assertSame(
            'Cannot execute method: add on Money object: OnMoon\Money\Money with other Money object as argument: OnMoon\Money\GaapMoney. The classes have different subunits: 2 and 4.',
            $exception->getMessage(),
        );
    }
}
