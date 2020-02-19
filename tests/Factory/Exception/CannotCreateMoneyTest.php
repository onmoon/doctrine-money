<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests\Factory\Exception;

use OnMoon\Money\Factory\Exception\CannotCreateMoney;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class CannotCreateMoneyTest extends TestCase
{
    public function testBecauseCurrencyCodeNotAllowed() : void
    {
        $class = 'My\Money\Class';

        $exception = CannotCreateMoney::becauseClassNotExtendsBaseMoney($class);

        Assert::assertSame(
            'Cannot create object of Money class: My\Money\Class, the class must extend: OnMoon\Money\BaseMoney',
            $exception->getMessage()
        );
    }
}
