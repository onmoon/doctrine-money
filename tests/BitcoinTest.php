<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests;

use OnMoon\Money\Bitcoin;
use OnMoon\Money\Currency;
use OnMoon\Money\Exception\CannotCreateMoney;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class BitcoinTest extends TestCase
{
    public function testCreate(): void
    {
        $amount = '154.12345678';
        $code   = 'XBT';

        $currency = Currency::create($code);
        $money    = Bitcoin::create($amount, $currency);

        Assert::assertInstanceOf(Bitcoin::class, $money);
        Assert::assertSame($amount, $money->getAmount());
        Assert::assertSame($code, $money->getCurrency()->getCode());
    }

    public function testCreateWrongAmount(): void
    {
        $amount = '100.0000000';
        $code   = 'XBT';

        $currency = Currency::create($code);

        $this->expectException(CannotCreateMoney::class);
        $this->expectExceptionMessage('Invalid Bitcoin with amount: 100.0000000 and currency: XBT. Invalid amount format. The correct format is: /^-?\d+\.\d{8}$/.');

        $money = Bitcoin::create($amount, $currency);
    }

    public function testCreateWrongCurrency(): void
    {
        $amount = '100.00000000';
        $code   = 'EUR';

        $currency = Currency::create($code);

        $this->expectException(CannotCreateMoney::class);
        $this->expectExceptionMessage('Invalid Bitcoin with amount: 100.00000000 and currency: EUR. Currency not allowed.');

        $money = Bitcoin::create($amount, $currency);
    }
}
