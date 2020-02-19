<?php

declare(strict_types=1);

namespace OnMoon\Money\Exception;

use function Safe\sprintf;

final class CannotWorkWithMoney extends MoneyLogicError
{
    public static function becauseMoneyHasDifferentSubunit(
        string $method,
        string $class,
        string $otherClass,
        int $subunits,
        int $otherSubunits
    ) : self {
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
