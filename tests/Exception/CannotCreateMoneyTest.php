<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests\Exception;

use OnMoon\Money\Currency;
use OnMoon\Money\Exception\CannotCreateMoney;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class CannotCreateMoneyTest extends TestCase
{
    public function testBecauseCurrencyMustBeBTC() : void
    {
        $currency = 'EUR';

        $exception = CannotCreateMoney::becauseCurrencyMustBeBTC($currency);

        Assert::assertSame(
            'Cannot create Bitcoin with currency: EUR. Only allowed currency code for Bitcoin is XBT.',
            $exception->getMessage()
        );
    }

    public function testBecauseCurrencyExceedsSubunitLimit() : void
    {
        $name            = 'Money';
        $classSubUnits   = 2;
        $bitcoin         = 'XBT';
        $bitcoinSubunits = 8;

        $bitcoinCurrency = Currency::create($bitcoin, $bitcoinSubunits);

        $exception = CannotCreateMoney::becauseCurrencyExceedsSubunitLimit($name, $classSubUnits, $bitcoinCurrency);

        Assert::assertSame(
            'Cannot create Money with currency: XBT. The currency has more subunits: 8 then the maximum allowed: 2.',
            $exception->getMessage()
        );
    }

    public function testBecauseAmountFormatIsInvalid() : void
    {
        $name     = 'Money';
        $amount   = '10.000';
        $subunits = 2;
        $format   = '/^-?\d+\.\d{2}$/';

        $exception = CannotCreateMoney::becauseAmountFormatIsInvalid($name, $amount, $subunits, $format);

        Assert::assertSame(
            'Cannot create Money from amount: 10.000 - invalid amount format. The correct format is: /^-?\d+\.\d{2}$/.',
            $exception->getMessage()
        );
    }

    public function testBecauseAmountMustBeGreaterThanZero() : void
    {
        $name   = 'Money';
        $amount = '0.00';

        $exception = CannotCreateMoney::becauseAmountMustBeGreaterThanZero($name, $amount);

        Assert::assertSame(
            'Cannot create Money from amount: 0.00 - amount must be greater than zero.',
            $exception->getMessage()
        );
    }

    public function testBecauseAmountMustBeZeroOrGreater() : void
    {
        $name   = 'Money';
        $amount = '-0.01';

        $exception = CannotCreateMoney::becauseAmountMustBeZeroOrGreater($name, $amount);

        Assert::assertSame(
            'Cannot create Money from amount: -0.01 - amount must be zero or greater.',
            $exception->getMessage()
        );
    }

    public function testBecauseAmountMustBeZeroOrLess() : void
    {
        $name   = 'Money';
        $amount = '0.01';

        $exception = CannotCreateMoney::becauseAmountMustBeZeroOrLess($name, $amount);

        Assert::assertSame(
            'Cannot create Money from amount: 0.01 - amount must be zero or less.',
            $exception->getMessage()
        );
    }

    public function testBecauseAmountMustBeLessThanZero() : void
    {
        $name   = 'Money';
        $amount = '0.00';

        $exception = CannotCreateMoney::becauseAmountMustBeLessThanZero($name, $amount);

        Assert::assertSame(
            'Cannot create Money from amount: 0.00 - amount must be less than zero.',
            $exception->getMessage()
        );
    }
}
