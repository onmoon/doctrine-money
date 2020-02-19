<?php

declare(strict_types=1);

namespace OnMoon\Money\Exception;

use LogicException;
use Money\Exception as MoneyException;

abstract class MoneyLogicError extends LogicException implements MoneyException
{
}
