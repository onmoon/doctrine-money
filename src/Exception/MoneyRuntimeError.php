<?php

declare(strict_types=1);

namespace OnMoon\Money\Exception;

use Money\Exception as MoneyException;
use RuntimeException;

abstract class MoneyRuntimeError extends RuntimeException implements MoneyException
{
}
