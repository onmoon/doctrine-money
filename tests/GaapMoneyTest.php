<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests;

use OnMoon\Money\Currency;
use OnMoon\Money\GaapMoney;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class GaapMoneyTest extends TestCase
{
    public function testCreate() : void
    {
        $amount   = '100.0000';
        $code     = 'EUR';
        $subunits = 4;

        $currency = Currency::create($code, $subunits);
        $money    = GaapMoney::create($amount, $currency);

        Assert::assertInstanceOf(GaapMoney::class, $money);
        Assert::assertSame($amount, $money->getAmount());
        Assert::assertSame($currency, $money->getCurrency());
    }
}
