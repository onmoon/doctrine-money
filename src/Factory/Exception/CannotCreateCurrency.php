<?php

declare(strict_types=1);

namespace OnMoon\Money\Factory\Exception;

use OnMoon\Money\Exception\MoneyRuntimeError;
use function count;
use function implode;
use function Safe\sprintf;

class CannotCreateCurrency extends MoneyRuntimeError
{
    public static function becauseCurrencyCodeNotAllowed(string $code, string ...$allowedCodes) : self
    {
        return new self(
            sprintf(
                'Cannot create Currency with code: %s. Allowed codes are: %s',
                $code,
                count($allowedCodes) ? implode(', ', $allowedCodes) : 'none'
            )
        );
    }
}
