<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests;

use OnMoon\Money\BTC;
use OnMoon\Money\Currency;
use OnMoon\Money\Exception\CannotCreateMoney;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class BTCTest extends TestCase
{
    public function testCreate() : void
    {
        $amount   = '100.00000000';
        $code     = 'XBT';
        $subunits = 8;

        $currency = Currency::create($code, $subunits);
        $money    = BTC::create($amount, $currency);

        Assert::assertInstanceOf(BTC::class, $money);
        Assert::assertSame($amount, $money->getAmount());
        Assert::assertSame($currency, $money->getCurrency());
    }

    public function testCreateWrongCurrency() : void
    {
        $amount   = '100.00000000';
        $code     = 'EUR';
        $subunits = 8;

        $currency = Currency::create($code, $subunits);

        $this->expectException(CannotCreateMoney::class);
        $this->expectExceptionMessage('Cannot create Bitcoin with currency: EUR. Only allowed currency code for Bitcoin is XBT.');

        $money = BTC::create($amount, $currency);
    }
}
