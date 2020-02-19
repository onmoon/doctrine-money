<?php

declare(strict_types=1);

namespace OnMoon\Money\Exception;

use function Safe\sprintf;

final class CannotConvertCurrency extends MoneyRuntimeError
{
    public static function becauseBothCurrenciesSame(string $amount, string $fromCurrency, string $toCurrency) : self
    {
        return new self(
            sprintf(
                'Cannot convert %s %s to the same currency: %s',
                $amount,
                $fromCurrency,
                $toCurrency
            )
        );
    }
}
