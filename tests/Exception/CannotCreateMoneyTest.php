<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests\Exception;

use OnMoon\Money\Exception\CannotCreateMoney;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class CannotCreateMoneyTest extends TestCase
{
    public function testBecauseCurrencyNotAllowed() : void
    {
        $name     = 'Money';
        $amount   = '10.00';
        $currency = 'EUR';

        $exception = CannotCreateMoney::becauseCurrencyNotAllowed($name, $amount, $currency);

        Assert::assertSame(
            'Invalid Money with amount: 10.00 and currency: EUR. Currency not allowed.',
            $exception->getMessage()
        );
    }

    public function testBecauseAmountFormatIsInvalid() : void
    {
        $name     = 'Money';
        $amount   = '10.000';
        $currency = 'EUR';
        $format   = '/^-?\d+\.\d{2}$/';

        $exception = CannotCreateMoney::becauseAmountFormatIsInvalid($name, $amount, $currency, $format);

        Assert::assertSame(
            'Invalid Money with amount: 10.000 and currency: EUR. Invalid amount format. The correct format is: /^-?\d+\.\d{2}$/.',
            $exception->getMessage()
        );
    }

    public function testBecauseAmountMustBeGreaterThanZero() : void
    {
        $name     = 'Money';
        $amount   = '0.00';
        $currency = 'EUR';

        $exception = CannotCreateMoney::becauseAmountMustBeGreaterThanZero($name, $amount, $currency);

        Assert::assertSame(
            'Invalid Money with amount: 0.00 and currency: EUR. Amount must be greater than zero.',
            $exception->getMessage()
        );
    }

    public function testBecauseAmountMustBeZeroOrGreater() : void
    {
        $name     = 'Money';
        $amount   = '-0.01';
        $currency = 'EUR';

        $exception = CannotCreateMoney::becauseAmountMustBeZeroOrGreater($name, $amount, $currency);

        Assert::assertSame(
            'Invalid Money with amount: -0.01 and currency: EUR. Amount must be zero or greater.',
            $exception->getMessage()
        );
    }

    public function testBecauseAmountMustBeZeroOrLess() : void
    {
        $name     = 'Money';
        $amount   = '0.01';
        $currency = 'EUR';

        $exception = CannotCreateMoney::becauseAmountMustBeZeroOrLess($name, $amount, $currency);

        Assert::assertSame(
            'Invalid Money with amount: 0.01 and currency: EUR. Amount must be zero or less.',
            $exception->getMessage()
        );
    }

    public function testBecauseAmountMustBeLessThanZero() : void
    {
        $name     = 'Money';
        $amount   = '0.00';
        $currency = 'EUR';

        $exception = CannotCreateMoney::becauseAmountMustBeLessThanZero($name, $amount, $currency);

        Assert::assertSame(
            'Invalid Money with amount: 0.00 and currency: EUR. Amount must be less than zero.',
            $exception->getMessage()
        );
    }
}
