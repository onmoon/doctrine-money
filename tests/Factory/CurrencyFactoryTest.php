<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests\Factory;

use Money\Currencies\CurrencyList;
use OnMoon\Money\Currency;
use OnMoon\Money\Factory\CurrencyFactory;
use OnMoon\Money\Factory\Exception\CannotCreateCurrency;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use function array_keys;

class CurrencyFactoryTest extends TestCase
{
    public function testCreateEmptyGlobalContext() : void
    {
        $factory = new CurrencyFactory();

        Assert::assertCount(0, $factory->getAllowedCurrencies());
    }

    public function testCreateGlobalContext() : void
    {
        $currencies = ['EUR' => 2, 'USD' => 2];
        $factory    = new CurrencyFactory(new CurrencyList($currencies));

        Assert::assertSame(array_keys($currencies), $factory->getAllowedCurrencies());
    }

    public function testRegisterContext() : void
    {
        $currencies = ['EUR' => 2, 'USD' => 2];
        $context    = 'context';
        $factory    = new CurrencyFactory();

        $factory->registerContext($context, new CurrencyList($currencies));

        Assert::assertCount(0, $factory->getAllowedCurrencies());
        Assert::assertSame(array_keys($currencies), $factory->getAllowedCurrencies($context));
    }

    public function testCreateInGlobalContext() : void
    {
        $euro        = 'EUR';
        $euroSubunit = 2;
        $currencies  = [$euro => $euroSubunit, 'USD' => 2];
        $factory     = new CurrencyFactory(new CurrencyList($currencies));

        $currency = $factory->create($euro);

        Assert::assertInstanceOf(Currency::class, $currency);
        Assert::assertSame($euroSubunit, $currency->getSubUnits());
        Assert::assertSame($euro, $currency->getCode());
    }

    public function testCreateInCustomContext() : void
    {
        $euro        = 'EUR';
        $euroSubunit = 2;
        $currencies  = [$euro => $euroSubunit, 'USD' => 2];
        $context     = 'context';
        $factory     = new CurrencyFactory();

        $factory->registerContext($context, new CurrencyList($currencies));

        $currency = $factory->create($euro, $context);

        Assert::assertInstanceOf(Currency::class, $currency);
        Assert::assertSame($euroSubunit, $currency->getSubUnits());
        Assert::assertSame($euro, $currency->getCode());
    }

    public function testCreateInInexistingContext() : void
    {
        $euro              = 'EUR';
        $euroSubunit       = 2;
        $currencies        = [$euro => $euroSubunit, 'USD' => 2];
        $context           = 'context';
        $inexistingContext = 'inexisting context';
        $factory           = new CurrencyFactory();

        $factory->registerContext($context, new CurrencyList($currencies));

        $this->expectException(CannotCreateCurrency::class);
        $this->expectExceptionMessage('Cannot create Currency with code: EUR. Allowed codes are: none');

        $currency = $factory->create($euro, $inexistingContext);
    }

    public function testCreateInexistingCurrency() : void
    {
        $euro        = 'EUR';
        $rouble      = 'RUB';
        $euroSubunit = 2;
        $currencies  = [$euro => $euroSubunit, 'USD' => 2];
        $context     = 'context';
        $factory     = new CurrencyFactory();

        $factory->registerContext($context, new CurrencyList($currencies));

        $this->expectException(CannotCreateCurrency::class);
        $this->expectExceptionMessage('Cannot create Currency with code: RUB. Allowed codes are: EUR, USD');

        $currency = $factory->create($rouble, $context);
    }
}
