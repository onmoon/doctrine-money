<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests;

use Money\Currencies\CurrencyList;
use OnMoon\Money\Currency;
use OnMoon\Money\Tests\Mocks\ExtendedCurrency;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    public function testCreateBaseClass() : void
    {
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);

        Assert::assertInstanceOf(Currency::class, $currency);
        Assert::assertSame($code, $currency->getCode());
        Assert::assertSame($subunits, $currency->getSubUnits());
    }

    public function testCreateExtendedClass() : void
    {
        $code     = 'EUR';
        $subunits = 2;

        $currency = ExtendedCurrency::create($code, $subunits);

        Assert::assertInstanceOf(ExtendedCurrency::class, $currency);
    }

    public function testEquals() : void
    {
        $firstCurrencyCode  = 'EUR';
        $secondCurrencyCode = 'EUR';
        $thirdCurrencyCode  = 'USD';
        $subunits           = 2;

        $firstCurrency  = Currency::create($firstCurrencyCode, $subunits);
        $secondCurrency = Currency::create($secondCurrencyCode, $subunits);
        $thirdCurrency  = Currency::create($thirdCurrencyCode, $subunits);

        Assert::assertTrue($firstCurrency->equals($secondCurrency));
        Assert::assertFalse($firstCurrency->equals($thirdCurrency));
    }

    public function testIsAvailableWithin() : void
    {
        $firstCurrencyCode  = 'EUR';
        $secondCurrencyCode = 'USD';
        $subunits           = 2;

        $firstCurrency  = Currency::create($firstCurrencyCode, $subunits);
        $secondCurrency = Currency::create($secondCurrencyCode, $subunits);

        $currencies = new CurrencyList([$firstCurrencyCode => $subunits]);

        Assert::assertTrue($firstCurrency->isAvailableWithin($currencies));
        Assert::assertFalse($secondCurrency->isAvailableWithin($currencies));
    }

    public function testToString() : void
    {
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);

        Assert::assertSame($code, (string) $currency);
    }

    public function testJsonSerialize() : void
    {
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);

        Assert::assertSame($code, $currency->jsonSerialize());
    }
}
