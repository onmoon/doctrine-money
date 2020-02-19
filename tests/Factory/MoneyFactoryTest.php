<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests\Factory;

use Money\Currencies\CurrencyList;
use OnMoon\Money\BaseMoney;
use OnMoon\Money\Factory\CurrencyFactory;
use OnMoon\Money\Factory\Exception\CannotCreateMoney;
use OnMoon\Money\Factory\MoneyFactory;
use OnMoon\Money\Money;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class MoneyFactoryTest extends TestCase
{
    public function testCreate() : void
    {
        $euro            = 'EUR';
        $currencies      = [$euro => 2, 'USD' => 2];
        $currencyFactory = new CurrencyFactory(new CurrencyList($currencies));
        $moneyClass      = Money::class;
        $amount          = '100.00';
        $moneyFactory    = new MoneyFactory($currencyFactory);

        $money = $moneyFactory->create($amount, $euro, $moneyClass);

        Assert::assertInstanceOf($moneyClass, $money);
        Assert::assertSame($amount, $money->getAmount());
        Assert::assertSame($euro, $money->getCurrency()->getCode());
    }

    public function testCreateInvalidClassError() : void
    {
        $euro            = 'EUR';
        $currencies      = [$euro => 2, 'USD' => 2];
        $currencyFactory = new CurrencyFactory(new CurrencyList($currencies));
        $moneyClass      = BaseMoney::class;
        $amount          = '100.00';
        $moneyFactory    = new MoneyFactory($currencyFactory);

        $this->expectException(CannotCreateMoney::class);
        $this->expectExceptionMessage('Cannot create object of Money class: OnMoon\Money\BaseMoney, the class must extend: OnMoon\Money\BaseMoney');

        $money = $moneyFactory->create($amount, $euro, $moneyClass);
    }
}
