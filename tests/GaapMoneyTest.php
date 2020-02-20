<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests;

use OnMoon\Money\Currency;
use OnMoon\Money\Exception\CannotCreateMoney;
use OnMoon\Money\GaapMoney;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class GaapMoneyTest extends TestCase
{
    /**
     * @dataProvider moneyProvider
     */
    public function testCreate(string $amount, string $code) : void
    {
        $currency = Currency::create($code);
        $money    = GaapMoney::create($amount, $currency);

        Assert::assertInstanceOf(GaapMoney::class, $money);
        Assert::assertSame($amount, $money->getAmount());
        Assert::assertSame($code, $money->getCurrency()->getCode());
    }

    public function testCreateInvalidCurrency() : void
    {
        $amount   = '10.00000000';
        $currency = 'XBT';

        $currency = Currency::create($currency);

        $this->expectException(CannotCreateMoney::class);
        $this->expectExceptionMessage('Invalid Money with amount: 10.00000000 and currency: XBT. Currency not allowed.');

        $money = GaapMoney::create($amount, $currency);
    }

    /**
     * @return mixed[][]
     */
    public function moneyProvider() : array
    {
        return [
            ['100', 'CLP'],
            ['100.12', 'EUR'],
            ['100.123', 'BHD'],
            ['100.1234', 'CLF'],
        ];
    }
}
