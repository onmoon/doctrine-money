<?php

declare(strict_types=1);

namespace OnMoon\Money;

use Money\Converter;
use Money\Currency as LibCurrency;
use Money\Money as LibMoney;
use OnMoon\Money\Exception\CannotConvertCurrency;
use OnMoon\Money\Exception\CannotCreateMoney;
use OnMoon\Money\Exception\CannotWorkWithMoney;
use function array_map;
use function bcdiv;
use function bcmul;
use function get_class;
use function Safe\preg_match;
use function str_pad;
use const STR_PAD_RIGHT;

abstract class BaseMoney
{
    private const HUMAN_READABLE_NAME = 'Money';

    /** @var string */
    private $amount;

    /** @var Currency */
    private $currency;

    final private function __construct(string $amount, Currency $currency)
    {
        if ($currency->getSubUnits() > static::subUnits()) {
            throw CannotCreateMoney::becauseCurrencyExceedsSubunitLimit(
                static::humanReadableName(),
                static::subUnits(),
                $currency
            );
        }

        if (! preg_match($this->getAmountFormatRegexp($currency->getSubUnits()), $amount)) {
            throw CannotCreateMoney::becauseAmountFormatIsInvalid(
                static::humanReadableName(),
                $amount,
                $currency->getSubUnits(),
                $this->getAmountFormatRegexp($currency->getSubUnits())
            );
        }

        $this->amount   = static::toSubunits($amount);
        $this->currency = $currency;
    }

    final public static function create(string $amount, Currency $currency) : self
    {
        $money = new static($amount, $currency);

        $money::validate($money);

        if ($money::amountMustBeGreaterThanZero() && ! $money->isPositive()) {
            throw CannotCreateMoney::becauseAmountMustBeGreaterThanZero(static::humanReadableName(), $amount);
        }

        if ($money::amountMustBeZeroOrGreater() && $money->isNegative()) {
            throw CannotCreateMoney::becauseAmountMustBeZeroOrGreater(static::humanReadableName(), $amount);
        }

        if ($money::amountMustBeZeroOrLess() && $money->isPositive()) {
            throw CannotCreateMoney::becauseAmountMustBeZeroOrLess(static::humanReadableName(), $amount);
        }

        if ($money::amountMustBeLessThanZero() && ! $money->isNegative()) {
            throw CannotCreateMoney::becauseAmountMustBeLessThanZero(static::humanReadableName(), $amount);
        }

        return $money;
    }

    final public static function createFromMoney(self $money) : self
    {
        static::assertSameSubUnit($money, __FUNCTION__);

        return static::create(
            $money->getAmount(),
            Currency::create(
                $money->getCurrency()->getCode(),
                $money->getCurrency()->getSubUnits()
            )
        );
    }

    final public static function fromSubunits(string $amount) : string
    {
        return (string) bcdiv($amount, str_pad('1', static::subUnits() + 1, '0', STR_PAD_RIGHT), static::subUnits());
    }

    final public static function toSubunits(string $amount) : string
    {
        return (string) bcmul($amount, str_pad('1', static::subUnits() + 1, '0', STR_PAD_RIGHT), 0);
    }

    final public function convert(Converter $converter, Currency $toCurrency) : self
    {
        if ($toCurrency->equals($this->getCurrency())) {
            throw CannotConvertCurrency::becauseBothCurrenciesSame(
                (string) $this,
                (string) $this->currency,
                (string) $toCurrency
            );
        }

        return self::createFromLibMoney(
            $converter->convert($this->getLibMoney(), new LibCurrency((string) $toCurrency)),
            $toCurrency
        );
    }

    // phpcs:disable Squiz.Commenting.FunctionComment.WrongStyle
    // Methods from original Money library

    final public function isSameCurrency(self $other) : bool
    {
        return $this->getLibMoney()->isSameCurrency($other->getLibMoney());
    }

    final public function equals(self $other) : bool
    {
        static::assertSameSubUnit($other, __FUNCTION__);

        return $this->getLibMoney()->equals($other->getLibMoney());
    }

    final public function compare(self $other) : int
    {
        static::assertSameSubUnit($other, __FUNCTION__);

        return $this->getLibMoney()->compare($other->getLibMoney());
    }

    final public function greaterThan(self $other) : bool
    {
        static::assertSameSubUnit($other, __FUNCTION__);

        return $this->getLibMoney()->greaterThan($other->getLibMoney());
    }

    final public function greaterThanOrEqual(self $other) : bool
    {
        static::assertSameSubUnit($other, __FUNCTION__);

        return $this->getLibMoney()->greaterThanOrEqual($other->getLibMoney());
    }

    final public function lessThan(self $other) : bool
    {
        static::assertSameSubUnit($other, __FUNCTION__);

        return $this->getLibMoney()->lessThan($other->getLibMoney());
    }

    final public function lessThanOrEqual(self $other) : bool
    {
        static::assertSameSubUnit($other, __FUNCTION__);

        return $this->getLibMoney()->lessThanOrEqual($other->getLibMoney());
    }

    final public function getAmount() : string
    {
        return self::fromSubunits($this->amount);
    }

    final public function getCurrency() : Currency
    {
        return $this->currency;
    }

    final public function add(self ...$addends) : self
    {
        return self::createFromLibMoney(
            $this->getLibMoney()->add(
                ...array_map(
                    static function (self $addend) : LibMoney {
                        static::assertSameSubUnit($addend, 'add');

                        return $addend->getLibMoney();
                    },
                    $addends
                )
            ),
            $this->currency
        );
    }

    final public function subtract(self ...$subtrahends) : self
    {
        return self::createFromLibMoney(
            $this->getLibMoney()->subtract(
                ...array_map(
                    static function (self $subtrahend) : LibMoney {
                        static::assertSameSubUnit($subtrahend, 'subtract');

                        return $subtrahend->getLibMoney();
                    },
                    $subtrahends
                )
            ),
            $this->currency
        );
    }

    final public function multiply(string $multiplier, int $roundingMode = LibMoney::ROUND_UP) : self
    {
        return self::createFromLibMoney(
            $this->getLibMoney()->multiply($multiplier, $roundingMode),
            $this->currency
        );
    }

    final public function divide(string $divisor, int $roundingMode = LibMoney::ROUND_UP) : self
    {
        return self::createFromLibMoney(
            $this->getLibMoney()->divide($divisor, $roundingMode),
            $this->currency
        );
    }

    final public function mod(self $divisor) : self
    {
        static::assertSameSubUnit($divisor, __FUNCTION__);

        return self::createFromLibMoney(
            $this->getLibMoney()->mod($divisor->getLibMoney()),
            $this->currency
        );
    }

    /**
     * @return self[]
     */
    final public function allocate(string ...$ratios) : array
    {
        return array_map(
            function (LibMoney $money) : self {
                return $this->createFromLibMoney($money, $this->currency);
            },
            $this->getLibMoney()->allocate($ratios)
        );
    }

    /**
     * @return self[]
     */
    final public function allocateTo(int $n) : array
    {
        return array_map(
            function (LibMoney $money) : self {
                return $this->createFromLibMoney($money, $this->currency);
            },
            $this->getLibMoney()->allocateTo($n)
        );
    }

    final public function ratioOf(self $money) : string
    {
        static::assertSameSubUnit($money, __FUNCTION__);

        return $this->getLibMoney()->ratioOf($money->getLibMoney());
    }

    final public function absolute() : self
    {
        return self::createFromLibMoney($this->getLibMoney()->absolute(), $this->currency);
    }

    final public function negative() : self
    {
        return self::createFromLibMoney($this->getLibMoney()->negative(), $this->currency);
    }

    final public function isZero() : bool
    {
        return $this->getLibMoney()->isZero();
    }

    final public function isPositive() : bool
    {
        return $this->getLibMoney()->isPositive();
    }

    final public function isNegative() : bool
    {
        return $this->getLibMoney()->isNegative();
    }

    /**
     * @return string[]
     */
    final public function jsonSerialize() : array
    {
        return [
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency()->jsonSerialize(),
        ];
    }

    final public static function min(self $first, self ...$collection) : self
    {
        $min = $first;

        foreach ($collection as $money) {
            $first::assertSameSubUnit($money, __FUNCTION__);

            if ($money->greaterThanOrEqual($min)) {
                continue;
            }

            $min = $money;
        }

        return $min;
    }

    final public static function max(self $first, self ...$collection) : self
    {
        $max = $first;

        foreach ($collection as $money) {
            $first::assertSameSubUnit($money, __FUNCTION__);

            if ($money->lessThanOrEqual($max)) {
                continue;
            }

            $max = $money;
        }

        return $max;
    }

    final public static function sum(self $first, self ...$collection) : self
    {
        $method = __FUNCTION__;

        return $first->createFromLibMoney(
            $first->getLibMoney()::sum(
                $first->getLibMoney(),
                ...array_map(
                    static function (self $money) use ($first, $method) : LibMoney {
                        $first::assertSameSubUnit($money, $method);

                        return $money->getLibMoney();
                    },
                    $collection
                )
            ),
            $first->getCurrency()
        );
    }

    final public static function avg(self $first, self ...$collection) : self
    {
        $method = __FUNCTION__;

        return $first->createFromLibMoney(
            $first->getLibMoney()::avg(
                $first->getLibMoney(),
                ...array_map(
                    static function (self $money) use ($first, $method) : LibMoney {
                        $first::assertSameSubUnit($money, $method);

                        return $money->getLibMoney();
                    },
                    $collection
                )
            ),
            $first->getCurrency()
        );
    }

    public function __toString() : string
    {
        return $this->getAmount() . ' ' . (string) $this->getCurrency();
    }

    abstract protected static function subUnits() : int;

    protected static function humanReadableName() : string
    {
        return self::HUMAN_READABLE_NAME;
    }

    protected static function amountMustBeZeroOrGreater() : bool
    {
        return false;
    }

    protected static function amountMustBeGreaterThanZero() : bool
    {
        return false;
    }

    protected static function amountMustBeZeroOrLess() : bool
    {
        return false;
    }

    protected static function amountMustBeLessThanZero() : bool
    {
        return false;
    }

    /**
     * @throws CannotCreateMoney
     */
    protected static function validate(self $money) : void
    {
    }

    private function createFromLibMoney(LibMoney $money, Currency $currency) : self
    {
        return self::create(
            self::fromSubunits($money->getAmount()),
            Currency::create($currency->getCode(), $currency->getSubUnits())
        );
    }

    private function getLibMoney() : LibMoney
    {
        return new LibMoney($this->amount, new LibCurrency($this->currency->getCode()));
    }

    private static function assertSameSubUnit(self $money, string $methodName) : void
    {
        if ($money::subUnits() !== static::subUnits()) {
            throw CannotWorkWithMoney::becauseMoneyHasDifferentSubunit(
                $methodName,
                static::class,
                get_class($money),
                static::subUnits(),
                $money::subUnits()
            );
        }
    }

    private function getAmountFormatRegexp(int $subunits) : string
    {
        return '/^-?\d+' . ($subunits > 0 ? '\.\d{' . $subunits . '}' : '') . '$/';
    }
}
