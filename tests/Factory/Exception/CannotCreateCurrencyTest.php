<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests\Factory\Exception;

use OnMoon\Money\Factory\Exception\CannotCreateCurrency;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class CannotCreateCurrencyTest extends TestCase
{
    public function testBecauseCurrencyCodeNotAllowed() : void
    {
        $code         = 'RUB';
        $allowedCodes = ['EUR', 'USD'];

        $exception = CannotCreateCurrency::becauseCurrencyCodeNotAllowed($code, ...$allowedCodes);

        Assert::assertSame(
            'Cannot create Currency with code: RUB. Allowed codes are: EUR, USD',
            $exception->getMessage()
        );
    }
}
