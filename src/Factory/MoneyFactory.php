<?php

declare(strict_types=1);

namespace OnMoon\Money\Factory;

use OnMoon\Money\BaseMoney;
use OnMoon\Money\Factory\Exception\CannotCreateMoney;
use function call_user_func;
use function is_subclass_of;

final class MoneyFactory
{
    /** @var CurrencyFactory */
    private $currencyFactory;

    public function __construct(CurrencyFactory $currencyFactory)
    {
        $this->currencyFactory = $currencyFactory;
    }

    /**
     * @psalm-param class-string<BaseMoney> $class
     */
    public function create(
        string $amount,
        string $code,
        string $class,
        string $context = 'global'
    ) : BaseMoney {
        if (! is_subclass_of($class, BaseMoney::class)) {
            throw CannotCreateMoney::becauseClassNotExtendsBaseMoney($class);
        }

        /** @psalm-var BaseMoney $money */
        $money = call_user_func(
            [$class, 'create'],
            $amount,
            $this->currencyFactory->create($code, $context)
        );

        return $money;
    }
}
