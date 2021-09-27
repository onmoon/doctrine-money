<?php

declare(strict_types=1);

namespace OnMoon\Money\Exception;

use function Safe\sprintf;

final class CannotWorkWithMoney extends MoneyLogicError
{
    public static function becauseCurrencyExceedsSubunitLimit(
        string $class,
        string $amount,
        string $currency,
        int $currencySubunits,
        int $classSubunits
    ): self {
        return new self(
            sprintf(
                'Cannot instantiate %s with amount: %s and currency: %s. The currency has more subunits: %s then the class allows: %s.',
                $class,
                $amount,
                $currency,
                $currencySubunits,
                $classSubunits
            )
        );
    }

    public static function becauseMoneyHasDifferentSubunit(
        string $method,
        string $class,
        string $otherClass,
        int $subunits,
        int $otherSubunits
    ): self {
        return new self(
            sprintf(
                'Cannot execute method: %s on Money object: %s with other Money object as argument: %s. The classes have different subunits: %s and %s.',
                $method,
                $class,
                $otherClass,
                $subunits,
                $otherSubunits
            )
        );
    }
}
