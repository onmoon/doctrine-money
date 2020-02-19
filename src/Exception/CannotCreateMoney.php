<?php

declare(strict_types=1);

namespace OnMoon\Money\Exception;

use OnMoon\Money\Currency;
use function Safe\sprintf;

final class CannotCreateMoney extends MoneyRuntimeError
{
    public static function becauseCurrencyMustBeBTC(string $currency) : self
    {
        return new self(
            sprintf(
                'Cannot create Bitcoin with currency: %s. Only allowed currency code for Bitcoin is XBT.',
                $currency
            )
        );
    }

    public static function becauseCurrencyExceedsSubunitLimit(string $name, int $classSubUnits, Currency $currency) : self
    {
        return new self(
            sprintf(
                'Cannot create %s with currency: %s. The currency has more subunits: %s then the maximum allowed: %s.',
                $name,
                $currency->getCode(),
                $currency->getSubUnits(),
                $classSubUnits
            )
        );
    }

    public static function becauseAmountFormatIsInvalid(string $name, string $amount, int $subUnits, string $format) : self
    {
        return new self(
            sprintf(
                'Cannot create %s from amount: %s - invalid amount format. The correct format is: %s.',
                $name,
                $amount,
                $format
            )
        );
    }

    public static function becauseAmountMustBeGreaterThanZero(string $name, string $amount) : self
    {
        return new self(
            sprintf(
                'Cannot create %s from amount: %s - amount must be greater than zero.',
                $name,
                $amount
            )
        );
    }

    public static function becauseAmountMustBeZeroOrGreater(string $name, string $amount) : self
    {
        return new self(
            sprintf(
                'Cannot create %s from amount: %s - amount must be zero or greater.',
                $name,
                $amount
            )
        );
    }

    public static function becauseAmountMustBeZeroOrLess(string $name, string $amount) : self
    {
        return new self(
            sprintf(
                'Cannot create %s from amount: %s - amount must be zero or less.',
                $name,
                $amount
            )
        );
    }

    public static function becauseAmountMustBeLessThanZero(string $name, string $amount) : self
    {
        return new self(
            sprintf(
                'Cannot create %s from amount: %s - amount must be less than zero.',
                $name,
                $amount
            )
        );
    }
}
