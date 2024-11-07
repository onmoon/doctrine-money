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
    public function testCreateBaseClass(): void
    {
        $code = 'EUR';

        $currency = Currency::create($code);

        Assert::assertInstanceOf(Currency::class, $currency);
        Assert::assertSame($code, $currency->getCode());
    }

    public function testCreateExtendedClass(): void
    {
        $code = 'EUR';

        $currency = ExtendedCurrency::create($code);

        Assert::assertInstanceOf(ExtendedCurrency::class, $currency);
    }

    public function testEquals(): void
    {
        $firstCurrencyCode  = 'EUR';
        $secondCurrencyCode = 'EUR';
        $thirdCurrencyCode  = 'USD';

        $firstCurrency  = Currency::create($firstCurrencyCode);
        $secondCurrency = Currency::create($secondCurrencyCode);
        $thirdCurrency  = Currency::create($thirdCurrencyCode);

        Assert::assertTrue($firstCurrency->equals($secondCurrency));
        Assert::assertFalse($firstCurrency->equals($thirdCurrency));
    }

    public function testIsAvailableWithin(): void
    {
        $firstCurrencyCode  = 'EUR';
        $secondCurrencyCode = 'USD';

        $firstCurrency  = Currency::create($firstCurrencyCode);
        $secondCurrency = Currency::create($secondCurrencyCode);

        $currencies = new CurrencyList([$firstCurrencyCode => 2]);

        Assert::assertTrue($firstCurrency->isAvailableWithin($currencies));
        Assert::assertFalse($secondCurrency->isAvailableWithin($currencies));
    }

    public function testToString(): void
    {
        $code = 'EUR';

        $currency = Currency::create($code);

        Assert::assertSame($code, (string) $currency);
    }

    public function testJsonSerialize(): void
    {
        $code = 'EUR';

        $currency = Currency::create($code);

        Assert::assertSame($code, $currency->jsonSerialize());
    }
}
