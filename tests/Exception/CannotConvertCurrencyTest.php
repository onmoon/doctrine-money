<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests\Exception;

use OnMoon\Money\Exception\CannotConvertCurrency;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class CannotConvertCurrencyTest extends TestCase
{
    public function testBecauseBothCurrenciesSame() : void
    {
        $amount       = '10.00';
        $fromCurrency = 'EUR';
        $toCurrency   = 'EUR';

        $exception = CannotConvertCurrency::becauseBothCurrenciesSame($amount, $fromCurrency, $toCurrency);

        Assert::assertSame(
            'Cannot convert 10.00 EUR to the same currency: EUR',
            $exception->getMessage()
        );
    }
}
