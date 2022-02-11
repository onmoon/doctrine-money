<?php

declare(strict_types=1);

namespace OnMoon\Money\Exception;

use function sprintf;

final class CannotCreateMoney extends MoneyRuntimeError
{
    public static function becauseCurrencyNotAllowed(
        string $name,
        string $amount,
        string $currency
    ): self {
        return new self(
            sprintf(
                'Invalid %s with amount: %s and currency: %s. Currency not allowed.',
                $name,
                $amount,
                $currency
            )
        );
    }

    public static function becauseAmountFormatIsInvalid(
        string $name,
        string $amount,
        string $currency,
        string $format
    ): self {
        return new self(
            sprintf(
                'Invalid %s with amount: %s and currency: %s. Invalid amount format. The correct format is: %s.',
                $name,
                $amount,
                $currency,
                $format
            )
        );
    }

    public static function becauseAmountMustBeGreaterThanZero(
        string $name,
        string $amount,
        string $currency
    ): self {
        return new self(
            sprintf(
                'Invalid %s with amount: %s and currency: %s. Amount must be greater than zero.',
                $name,
                $amount,
                $currency
            )
        );
    }

    public static function becauseAmountMustBeZeroOrGreater(
        string $name,
        string $amount,
        string $currency
    ): self {
        return new self(
            sprintf(
                'Invalid %s with amount: %s and currency: %s. Amount must be zero or greater.',
                $name,
                $amount,
                $currency
            )
        );
    }

    public static function becauseAmountMustBeZeroOrLess(
        string $name,
        string $amount,
        string $currency
    ): self {
        return new self(
            sprintf(
                'Invalid %s with amount: %s and currency: %s. Amount must be zero or less.',
                $name,
                $amount,
                $currency
            )
        );
    }

    public static function becauseAmountMustBeLessThanZero(
        string $name,
        string $amount,
        string $currency
    ): self {
        return new self(
            sprintf(
                'Invalid %s with amount: %s and currency: %s. Amount must be less than zero.',
                $name,
                $amount,
                $currency
            )
        );
    }
}
