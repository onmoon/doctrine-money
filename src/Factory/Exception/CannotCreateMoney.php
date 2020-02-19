<?php

declare(strict_types=1);

namespace OnMoon\Money\Factory\Exception;

use OnMoon\Money\BaseMoney;
use OnMoon\Money\Exception\MoneyLogicError;
use function Safe\sprintf;

class CannotCreateMoney extends MoneyLogicError
{
    public static function becauseClassNotExtendsBaseMoney(string $class) : self
    {
        return new self(
            sprintf(
                'Cannot create object of Money class: %s, the class must extend: %s',
                $class,
                BaseMoney::class
            )
        );
    }
}
