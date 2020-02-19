<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests;

use InvalidArgumentException;
use Money\Money as LibMoney;
use OnMoon\Money\Currency;
use OnMoon\Money\Exception\CannotCreateMoney;
use OnMoon\Money\Exception\CannotWorkWithMoney;
use OnMoon\Money\GaapMoney;
use OnMoon\Money\Money;
use OnMoon\Money\Tests\Mocks\AmountMustBeGreaterThanZeroMoney;
use OnMoon\Money\Tests\Mocks\AmountMustBeLessThanZeroMoney;
use OnMoon\Money\Tests\Mocks\AmountMustBeZeroOrGreaterMoney;
use OnMoon\Money\Tests\Mocks\AmountMustBeZeroOrLessMoney;
use OnMoon\Money\Tests\Mocks\CheckAmountMoney;
use OnMoon\Money\Tests\Mocks\ExtendedMoney;
use OnMoon\Money\Tests\Mocks\ZeroSubunitMoney;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function testCreate() : void
    {
        $amount   = '100.00';
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);
        $money    = Money::create($amount, $currency);

        Assert::assertInstanceOf(Money::class, $money);
        Assert::assertSame($amount, $money->getAmount());
        Assert::assertSame($currency, $money->getCurrency());
    }

    public function testCreateExtendedClass() : void
    {
        $amount   = '100.00';
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);
        $money    = ExtendedMoney::create($amount, $currency);

        Assert::assertInstanceOf(ExtendedMoney::class, $money);
        Assert::assertSame($amount, $money->getAmount());
        Assert::assertSame($currency, $money->getCurrency());
    }

    public function testCreateValidateSuccess() : void
    {
        $amount   = '100.00';
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);
        $money    = CheckAmountMoney::create($amount, $currency);

        Assert::assertInstanceOf(CheckAmountMoney::class, $money);
    }

    public function testCreateValidateFailure() : void
    {
        $amount   = '100.01';
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);

        $this->expectException(CannotCreateMoney::class);
        $this->expectExceptionMessage('Money amount is greater than 100.00 EUR');

        $money = CheckAmountMoney::create($amount, $currency);
    }

    public function testCanCreateWithNegativeAmount() : void
    {
        $amount   = '-0.01';
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);
        $money    = Money::create($amount, $currency);

        Assert::assertInstanceOf(Money::class, $money);
        Assert::assertSame($amount, $money->getAmount());
        Assert::assertSame($currency, $money->getCurrency());
    }

    public function testCanCreateWithZeroAmount() : void
    {
        $amount   = '0.00';
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);
        $money    = Money::create($amount, $currency);

        Assert::assertInstanceOf(Money::class, $money);
        Assert::assertSame($amount, $money->getAmount());
        Assert::assertSame($currency, $money->getCurrency());
    }

    public function testCanCreateWithPositiveAmount() : void
    {
        $amount   = '0.01';
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);
        $money    = Money::create($amount, $currency);

        Assert::assertInstanceOf(Money::class, $money);
        Assert::assertSame($amount, $money->getAmount());
        Assert::assertSame($currency, $money->getCurrency());
    }

    public function testCantCreateWithZeroAmountIfMustBeGreaterThanZeroApplyed() : void
    {
        $amount   = '0.00';
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);

        $this->expectException(CannotCreateMoney::class);
        $this->expectExceptionMessage('Cannot create Money from amount: 0.00 - amount must be greater than zero.');

        $money = AmountMustBeGreaterThanZeroMoney::create($amount, $currency);
    }

    public function testCantCreateWithNegativeAmountIfMustBeGreaterThanZeroApplyed() : void
    {
        $amount   = '-0.01';
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);

        $this->expectException(CannotCreateMoney::class);
        $this->expectExceptionMessage('Cannot create Money from amount: -0.01 - amount must be greater than zero.');

        $money = AmountMustBeGreaterThanZeroMoney::create($amount, $currency);
    }

    public function testCanCreateWithPositiveAmountIfMustBeGreaterThanZeroApplyed() : void
    {
        $amount   = '0.01';
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);
        $money    = AmountMustBeGreaterThanZeroMoney::create($amount, $currency);

        Assert::assertSame($amount, $money->getAmount());
        Assert::assertSame($currency, $money->getCurrency());
    }

    public function testCantCreateWithNegativeAmountIfMustBeZeroOrGreaterApplyed() : void
    {
        $amount   = '-0.01';
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);

        $this->expectException(CannotCreateMoney::class);
        $this->expectExceptionMessage('Cannot create Money from amount: -0.01 - amount must be zero or greater.');

        $money = AmountMustBeZeroOrGreaterMoney::create($amount, $currency);
    }

    public function testCanCreateWithZeroAmountIfMustBeZeroOrGreaterApplyed() : void
    {
        $amount   = '0.00';
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);
        $money    = AmountMustBeZeroOrGreaterMoney::create($amount, $currency);

        Assert::assertSame($amount, $money->getAmount());
        Assert::assertSame($currency, $money->getCurrency());
    }

    public function testCanCreateWithPositiveAmountIfMustBeZeroOrGreaterApplyed() : void
    {
        $amount   = '0.01';
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);
        $money    = AmountMustBeZeroOrGreaterMoney::create($amount, $currency);

        Assert::assertSame($amount, $money->getAmount());
        Assert::assertSame($currency, $money->getCurrency());
    }

    public function testCantCreateWithPositiveAmountIfMustBeZeroOrLessApplyed() : void
    {
        $amount   = '0.01';
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);

        $this->expectException(CannotCreateMoney::class);
        $this->expectExceptionMessage('Cannot create Money from amount: 0.01 - amount must be zero or less.');

        $money = AmountMustBeZeroOrLessMoney::create($amount, $currency);
    }

    public function testCanCreateWithZeroAmountIfMustBeZeroOrLessApplyed() : void
    {
        $amount   = '0.00';
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);
        $money    = AmountMustBeZeroOrLessMoney::create($amount, $currency);

        Assert::assertSame($amount, $money->getAmount());
        Assert::assertSame($currency, $money->getCurrency());
    }

    public function testCanCreateWithNegativeAmountIfMustBeZeroOrLessApplyed() : void
    {
        $amount   = '-0.01';
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);
        $money    = AmountMustBeZeroOrLessMoney::create($amount, $currency);

        Assert::assertSame($amount, $money->getAmount());
        Assert::assertSame($currency, $money->getCurrency());
    }

    public function testCantCreateWithPositiveAmountIfMustBeLessThanZeroApplyed() : void
    {
        $amount   = '0.01';
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);

        $this->expectException(CannotCreateMoney::class);
        $this->expectExceptionMessage('Cannot create Money from amount: 0.01 - amount must be less than zero.');

        $money = AmountMustBeLessThanZeroMoney::create($amount, $currency);
    }

    public function testCantCreateWithZeroAmountIfMustBeLessThanZeroApplyed() : void
    {
        $amount   = '0.00';
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);

        $this->expectException(CannotCreateMoney::class);
        $this->expectExceptionMessage('Cannot create Money from amount: 0.00 - amount must be less than zero.');

        $money = AmountMustBeLessThanZeroMoney::create($amount, $currency);
    }

    public function testCanCreateWithNegativeAmountIfMustBeLessThanZeroApplyed() : void
    {
        $amount   = '-0.01';
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);
        $money    = AmountMustBeLessThanZeroMoney::create($amount, $currency);

        Assert::assertSame($amount, $money->getAmount());
        Assert::assertSame($currency, $money->getCurrency());
    }

    public function testCantCreateWithInvalidAmountFormat() : void
    {
        $amount   = '10.000';
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);

        $this->expectException(CannotCreateMoney::class);
        $this->expectExceptionMessage('Cannot create Money from amount: 10.000 - invalid amount format. The correct format is: /^-?\d+\.\d{2}$/.');

        $money = Money::create($amount, $currency);
    }

    public function testCantCreateWithExceedingSubunitCurrency() : void
    {
        $amount   = '10.000';
        $code     = 'EUR';
        $subunits = 3;

        $currency = Currency::create($code, $subunits);

        $this->expectException(CannotCreateMoney::class);
        $this->expectExceptionMessage('Cannot create Money with currency: EUR. The currency has more subunits: 3 then the maximum allowed: 2.');

        $money = Money::create($amount, $currency);
    }

    public function testCanCreateFromSameSubunitMoney() : void
    {
        $amount   = '10.00';
        $code     = 'EUR';
        $subunits = 2;

        $currency   = Currency::create($code, $subunits);
        $money      = Money::create($amount, $currency);
        $otherMoney = ExtendedMoney::createFromMoney($money);

        Assert::assertInstanceOf(ExtendedMoney::class, $otherMoney);
        Assert::assertSame($amount, $otherMoney->getAmount());
        Assert::assertNotSame($currency, $otherMoney->getCurrency());
        Assert::assertSame($code, $otherMoney->getCurrency()->getCode());
    }

    public function testCantCreateFromOtherSubunitMoney() : void
    {
        $amount   = '10.00';
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);
        $money    = Money::create($amount, $currency);

        $this->expectException(CannotWorkWithMoney::class);
        $this->expectExceptionMessage('Cannot execute method: createFromMoney on Money object: OnMoon\Money\GaapMoney with other Money object as argument: OnMoon\Money\Money. The classes have different subunits: 4 and 2.');

        $otherMoney = GaapMoney::createFromMoney($money);
    }

    public function testCanCreateZeroSubunitMoney() : void
    {
        $amount   = '100';
        $code     = 'DJF';
        $subunits = 0;

        $currency = Currency::create($code, $subunits);
        $money    = ZeroSubunitMoney::create($amount, $currency);

        Assert::assertSame($amount, $money->getAmount());
    }

    public function testCreateZeroSubunitMoneyInvalidFormatError() : void
    {
        $amount   = '100.0';
        $code     = 'DJF';
        $subunits = 0;

        $currency = Currency::create($code, $subunits);

        $this->expectException(CannotCreateMoney::class);
        $this->expectExceptionMessage('Cannot create Money from amount: 100.0 - invalid amount format. The correct format is: /^-?\d+$/.');

        $money = ZeroSubunitMoney::create($amount, $currency);
    }

    public function testFromSubunits() : void
    {
        $subunitAmount = '123';
        $unitAmount    = '1.23';

        Assert::assertSame($unitAmount, Money::fromSubunits($subunitAmount));
    }

    public function testToSubunits() : void
    {
        $unitAmount    = '1.23';
        $subunitAmount = '123';

        Assert::assertSame($subunitAmount, Money::toSubunits($unitAmount));
    }

    public function testIsSameCurrency() : void
    {
        $amount     = '10.00';
        $firstCode  = 'EUR';
        $secondCode = 'EUR';
        $thirdCode  = 'USD';
        $subunits   = 2;

        $firstCurrency  = Currency::create($firstCode, $subunits);
        $firstMoney     = Money::create($amount, $firstCurrency);
        $secondCurrency = Currency::create($secondCode, $subunits);
        $secondMoney    = Money::create($amount, $secondCurrency);
        $thirdCurrency  = Currency::create($thirdCode, $subunits);
        $thirdMoney     = Money::create($amount, $thirdCurrency);

        Assert::assertTrue($firstMoney->isSameCurrency($secondMoney));
        Assert::assertFalse($firstMoney->isSameCurrency($thirdMoney));
    }

    public function testEquals() : void
    {
        $ten      = '10.00';
        $five     = '5.00';
        $euro     = 'EUR';
        $dollar   = 'USD';
        $subunits = 2;

        $euroCurrency    = Currency::create($euro, $subunits);
        $dollarCurrency  = Currency::create($dollar, $subunits);
        $tenEuros        = Money::create($ten, $euroCurrency);
        $anotherTenEuros = Money::create($ten, $euroCurrency);
        $fiveEuros       = Money::create($five, $euroCurrency);
        $tenDollars      = Money::create($ten, $dollarCurrency);

        Assert::assertTrue($tenEuros->equals($anotherTenEuros));
        Assert::assertFalse($tenEuros->equals($fiveEuros));
        Assert::assertFalse($tenEuros->equals($tenDollars));
    }

    public function testCantCheckEqualityWithOtherSubunitMoney() : void
    {
        $ten      = '10.00';
        $euro     = 'EUR';
        $subunits = 2;

        $euroCurrency    = Currency::create($euro, $subunits);
        $tenEuros        = Money::create($ten, $euroCurrency);
        $anotherTenEuros = GaapMoney::create($ten, $euroCurrency);

        $this->expectException(CannotWorkWithMoney::class);
        $this->expectExceptionMessage('Cannot execute method: equals on Money object: OnMoon\Money\Money with other Money object as argument: OnMoon\Money\GaapMoney. The classes have different subunits: 2 and 4.');

        $tenEuros->equals($anotherTenEuros);
    }

    public function testCompare() : void
    {
        $ten      = '10.00';
        $five     = '5.00';
        $fifteen  = '15.00';
        $euro     = 'EUR';
        $subunits = 2;

        $euroCurrency    = Currency::create($euro, $subunits);
        $tenEuros        = Money::create($ten, $euroCurrency);
        $anotherTenEuros = Money::create($ten, $euroCurrency);
        $fiveEuros       = Money::create($five, $euroCurrency);
        $fifteenEuros    = Money::create($fifteen, $euroCurrency);

        Assert::assertSame(0, $tenEuros->compare($anotherTenEuros));
        Assert::assertSame(1, $tenEuros->compare($fiveEuros));
        Assert::assertSame(-1, $tenEuros->compare($fifteenEuros));
    }

    public function testCantCompareWithOtherCurrencyMoney() : void
    {
        $ten      = '10.00';
        $euro     = 'EUR';
        $dollar   = 'USD';
        $subunits = 2;

        $euroCurrency   = Currency::create($euro, $subunits);
        $dollarCurrency = Currency::create($dollar, $subunits);
        $tenEuros       = Money::create($ten, $euroCurrency);
        $tenDollars     = Money::create($ten, $dollarCurrency);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Currencies must be identical');

        $tenEuros->compare($tenDollars);
    }

    public function testCantCompareWithOtherSubunitMoney() : void
    {
        $ten      = '10.00';
        $euro     = 'EUR';
        $subunits = 2;

        $euroCurrency    = Currency::create($euro, $subunits);
        $tenEuros        = Money::create($ten, $euroCurrency);
        $anotherTenEuros = GaapMoney::create($ten, $euroCurrency);

        $this->expectException(CannotWorkWithMoney::class);
        $this->expectExceptionMessage('Cannot execute method: compare on Money object: OnMoon\Money\Money with other Money object as argument: OnMoon\Money\GaapMoney. The classes have different subunits: 2 and 4.');

        $tenEuros->compare($anotherTenEuros);
    }

    public function testGreaterThan() : void
    {
        $ten      = '10.00';
        $five     = '5.00';
        $fifteen  = '15.00';
        $euro     = 'EUR';
        $subunits = 2;

        $euroCurrency    = Currency::create($euro, $subunits);
        $tenEuros        = Money::create($ten, $euroCurrency);
        $anotherTenEuros = Money::create($ten, $euroCurrency);
        $fiveEuros       = Money::create($five, $euroCurrency);
        $fifteenEuros    = Money::create($fifteen, $euroCurrency);

        Assert::assertFalse($tenEuros->greaterThan($anotherTenEuros));
        Assert::assertTrue($tenEuros->greaterThan($fiveEuros));
        Assert::assertFalse($tenEuros->greaterThan($fifteenEuros));
    }

    public function testCantCompareGreaterThanWithOtherSubunitMoney() : void
    {
        $ten      = '10.00';
        $euro     = 'EUR';
        $subunits = 2;

        $euroCurrency    = Currency::create($euro, $subunits);
        $tenEuros        = Money::create($ten, $euroCurrency);
        $anotherTenEuros = GaapMoney::create($ten, $euroCurrency);

        $this->expectException(CannotWorkWithMoney::class);
        $this->expectExceptionMessage('Cannot execute method: greaterThan on Money object: OnMoon\Money\Money with other Money object as argument: OnMoon\Money\GaapMoney. The classes have different subunits: 2 and 4.');

        $tenEuros->greaterThan($anotherTenEuros);
    }

    public function testGreaterThanOrEqual() : void
    {
        $ten      = '10.00';
        $five     = '5.00';
        $fifteen  = '15.00';
        $euro     = 'EUR';
        $subunits = 2;

        $euroCurrency    = Currency::create($euro, $subunits);
        $tenEuros        = Money::create($ten, $euroCurrency);
        $anotherTenEuros = Money::create($ten, $euroCurrency);
        $fiveEuros       = Money::create($five, $euroCurrency);
        $fifteenEuros    = Money::create($fifteen, $euroCurrency);

        Assert::assertTrue($tenEuros->greaterThanOrEqual($anotherTenEuros));
        Assert::assertTrue($tenEuros->greaterThanOrEqual($fiveEuros));
        Assert::assertFalse($tenEuros->greaterThanOrEqual($fifteenEuros));
    }

    public function testCantCompareGreaterThanOrEqualWithOtherSubunitMoney() : void
    {
        $ten      = '10.00';
        $euro     = 'EUR';
        $subunits = 2;

        $euroCurrency    = Currency::create($euro, $subunits);
        $tenEuros        = Money::create($ten, $euroCurrency);
        $anotherTenEuros = GaapMoney::create($ten, $euroCurrency);

        $this->expectException(CannotWorkWithMoney::class);
        $this->expectExceptionMessage('Cannot execute method: greaterThanOrEqual on Money object: OnMoon\Money\Money with other Money object as argument: OnMoon\Money\GaapMoney. The classes have different subunits: 2 and 4.');

        $tenEuros->greaterThanOrEqual($anotherTenEuros);
    }

    public function testLessThan() : void
    {
        $ten      = '10.00';
        $five     = '5.00';
        $fifteen  = '15.00';
        $euro     = 'EUR';
        $subunits = 2;

        $euroCurrency    = Currency::create($euro, $subunits);
        $tenEuros        = Money::create($ten, $euroCurrency);
        $anotherTenEuros = Money::create($ten, $euroCurrency);
        $fiveEuros       = Money::create($five, $euroCurrency);
        $fifteenEuros    = Money::create($fifteen, $euroCurrency);

        Assert::assertFalse($tenEuros->lessThan($anotherTenEuros));
        Assert::assertFalse($tenEuros->lessThan($fiveEuros));
        Assert::assertTrue($tenEuros->lessThan($fifteenEuros));
    }

    public function testCantCompareLessThanWithOtherSubunitMoney() : void
    {
        $ten      = '10.00';
        $euro     = 'EUR';
        $subunits = 2;

        $euroCurrency    = Currency::create($euro, $subunits);
        $tenEuros        = Money::create($ten, $euroCurrency);
        $anotherTenEuros = GaapMoney::create($ten, $euroCurrency);

        $this->expectException(CannotWorkWithMoney::class);
        $this->expectExceptionMessage('Cannot execute method: lessThan on Money object: OnMoon\Money\Money with other Money object as argument: OnMoon\Money\GaapMoney. The classes have different subunits: 2 and 4.');

        $tenEuros->lessThan($anotherTenEuros);
    }

    public function testLessThanOrEqual() : void
    {
        $ten      = '10.00';
        $five     = '5.00';
        $fifteen  = '15.00';
        $euro     = 'EUR';
        $subunits = 2;

        $euroCurrency    = Currency::create($euro, $subunits);
        $tenEuros        = Money::create($ten, $euroCurrency);
        $anotherTenEuros = Money::create($ten, $euroCurrency);
        $fiveEuros       = Money::create($five, $euroCurrency);
        $fifteenEuros    = Money::create($fifteen, $euroCurrency);

        Assert::assertTrue($tenEuros->lessThanOrEqual($anotherTenEuros));
        Assert::assertFalse($tenEuros->lessThanOrEqual($fiveEuros));
        Assert::assertTrue($tenEuros->lessThanOrEqual($fifteenEuros));
    }

    public function testCantCompareLessThanOrEqualWithOtherSubunitMoney() : void
    {
        $ten      = '10.00';
        $euro     = 'EUR';
        $subunits = 2;

        $euroCurrency    = Currency::create($euro, $subunits);
        $tenEuros        = Money::create($ten, $euroCurrency);
        $anotherTenEuros = GaapMoney::create($ten, $euroCurrency);

        $this->expectException(CannotWorkWithMoney::class);
        $this->expectExceptionMessage('Cannot execute method: lessThanOrEqual on Money object: OnMoon\Money\Money with other Money object as argument: OnMoon\Money\GaapMoney. The classes have different subunits: 2 and 4.');

        $tenEuros->lessThanOrEqual($anotherTenEuros);
    }

    public function testGetAmount() : void
    {
        $amount   = '12.34';
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);
        $money    = Money::create($amount, $currency);

        Assert::assertSame($amount, $money->getAmount());
    }

    public function testGetCurrency() : void
    {
        $amount   = '12.34';
        $code     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($code, $subunits);
        $money    = Money::create($amount, $currency);

        Assert::assertSame($currency, $money->getCurrency());
    }

    public function testAdd() : void
    {
        $ten         = '10.00';
        $minusFive   = '-5.00';
        $half        = '0.50';
        $fiveAndHalf = '5.50';
        $euro        = 'EUR';
        $subunits    = 2;

        $euroCurrency   = Currency::create($euro, $subunits);
        $tenEuros       = Money::create($ten, $euroCurrency);
        $minusFiveEuros = Money::create($minusFive, $euroCurrency);
        $halfEuro       = Money::create($half, $euroCurrency);

        $fiveAndHalfEuros = $tenEuros->add($minusFiveEuros, $halfEuro);

        Assert::assertInstanceOf(Money::class, $fiveAndHalfEuros);
        Assert::assertSame($fiveAndHalf, $fiveAndHalfEuros->getAmount());
        Assert::assertSame($euro, $fiveAndHalfEuros->getCurrency()->getCode());
    }

    public function testAddExtendedClassReturnsBaseClass() : void
    {
        $ten      = '10.00';
        $half     = '0.50';
        $euro     = 'EUR';
        $subunits = 2;

        $euroCurrency = Currency::create($euro, $subunits);
        $tenEuros     = ExtendedMoney::create($ten, $euroCurrency);
        $halfEuro     = ExtendedMoney::create($half, $euroCurrency);

        $tenAndHalfEuros = $tenEuros->add($halfEuro);

        Assert::assertInstanceOf(Money::class, $tenAndHalfEuros);
    }

    public function testCantAddOtherCurrencyMoney() : void
    {
        $ten      = '10.00';
        $euro     = 'EUR';
        $dollar   = 'USD';
        $subunits = 2;

        $euroCurrency   = Currency::create($euro, $subunits);
        $dollarCurrency = Currency::create($dollar, $subunits);
        $tenEuros       = Money::create($ten, $euroCurrency);
        $tenDollars     = Money::create($ten, $dollarCurrency);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Currencies must be identical');

        $tenEuros->add($tenDollars);
    }

    public function testCantAddOtherSubunitMoney() : void
    {
        $ten      = '10.00';
        $euro     = 'EUR';
        $subunits = 2;

        $euroCurrency    = Currency::create($euro, $subunits);
        $tenEuros        = Money::create($ten, $euroCurrency);
        $anotherTenEuros = GaapMoney::create($ten, $euroCurrency);

        $this->expectException(CannotWorkWithMoney::class);
        $this->expectExceptionMessage('Cannot execute method: add on Money object: OnMoon\Money\Money with other Money object as argument: OnMoon\Money\GaapMoney. The classes have different subunits: 2 and 4.');

        $tenEuros->add($anotherTenEuros);
    }

    public function testSubtract() : void
    {
        $ten             = '10.00';
        $minusFive       = '-5.00';
        $half            = '0.50';
        $fourteenAndHalf = '14.50';
        $euro            = 'EUR';
        $subunits        = 2;

        $euroCurrency   = Currency::create($euro, $subunits);
        $tenEuros       = Money::create($ten, $euroCurrency);
        $minusFiveEuros = Money::create($minusFive, $euroCurrency);
        $halfEuro       = Money::create($half, $euroCurrency);

        $fourteenAndHalfEuros = $tenEuros->subtract($minusFiveEuros, $halfEuro);

        Assert::assertInstanceOf(Money::class, $fourteenAndHalfEuros);
        Assert::assertSame($fourteenAndHalf, $fourteenAndHalfEuros->getAmount());
        Assert::assertSame($euro, $fourteenAndHalfEuros->getCurrency()->getCode());
    }

    public function testSubtractExtendedClassReturnsBaseClass() : void
    {
        $ten      = '10.00';
        $half     = '0.50';
        $euro     = 'EUR';
        $subunits = 2;

        $euroCurrency = Currency::create($euro, $subunits);
        $tenEuros     = ExtendedMoney::create($ten, $euroCurrency);
        $halfEuro     = ExtendedMoney::create($half, $euroCurrency);

        $nineAndHalfEuros = $tenEuros->subtract($halfEuro);

        Assert::assertInstanceOf(Money::class, $nineAndHalfEuros);
    }

    public function testCantSubtractOtherCurrencyMoney() : void
    {
        $ten      = '10.00';
        $euro     = 'EUR';
        $dollar   = 'USD';
        $subunits = 2;

        $euroCurrency   = Currency::create($euro, $subunits);
        $dollarCurrency = Currency::create($dollar, $subunits);
        $tenEuros       = Money::create($ten, $euroCurrency);
        $tenDollars     = Money::create($ten, $dollarCurrency);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Currencies must be identical');

        $tenEuros->subtract($tenDollars);
    }

    public function testCantSubtractOtherSubunitMoney() : void
    {
        $ten      = '10.00';
        $euro     = 'EUR';
        $subunits = 2;

        $euroCurrency    = Currency::create($euro, $subunits);
        $tenEuros        = Money::create($ten, $euroCurrency);
        $anotherTenEuros = GaapMoney::create($ten, $euroCurrency);

        $this->expectException(CannotWorkWithMoney::class);
        $this->expectExceptionMessage('Cannot execute method: subtract on Money object: OnMoon\Money\Money with other Money object as argument: OnMoon\Money\GaapMoney. The classes have different subunits: 2 and 4.');

        $tenEuros->subtract($anotherTenEuros);
    }

    public function testMultiply() : void
    {
        $amount           = '3.33';
        $multiplier       = '2.22';
        $multipliedAmount = '7.40';
        $euro             = 'EUR';
        $subunits         = 2;

        $currency = Currency::create($euro, $subunits);
        $money    = Money::create($amount, $currency);

        $multipliedMoney = $money->multiply($multiplier);

        Assert::assertInstanceOf(Money::class, $multipliedMoney);
        Assert::assertSame($multipliedAmount, $multipliedMoney->getAmount());
        Assert::assertSame($euro, $multipliedMoney->getCurrency()->getCode());
    }

    public function testMultiplyRoundingModeOtherThanDefault() : void
    {
        $amount           = '3.33';
        $multiplier       = '2.22';
        $multipliedAmount = '7.39';
        $euro             = 'EUR';
        $subunits         = 2;

        $currency = Currency::create($euro, $subunits);
        $money    = Money::create($amount, $currency);

        $multipliedMoney = $money->multiply($multiplier, LibMoney::ROUND_DOWN);

        Assert::assertSame($multipliedAmount, $multipliedMoney->getAmount());
    }

    public function testMultiplyExtendedClassReturnsBaseClass() : void
    {
        $amount     = '3.33';
        $multiplier = '2.22';
        $euro       = 'EUR';
        $subunits   = 2;

        $currency = Currency::create($euro, $subunits);
        $money    = ExtendedMoney::create($amount, $currency);

        $multipliedMoney = $money->multiply($multiplier);

        Assert::assertInstanceOf(Money::class, $multipliedMoney);
    }

    public function testDivide() : void
    {
        $amount        = '3.33';
        $divisor       = '1.55';
        $dividedAmount = '2.15';
        $euro          = 'EUR';
        $subunits      = 2;

        $currency = Currency::create($euro, $subunits);
        $money    = Money::create($amount, $currency);

        $dividedMoney = $money->divide($divisor);

        Assert::assertInstanceOf(Money::class, $dividedMoney);
        Assert::assertSame($dividedAmount, $dividedMoney->getAmount());
        Assert::assertSame($euro, $dividedMoney->getCurrency()->getCode());
    }

    public function testDivideRoundingModeOtherThanDefault() : void
    {
        $amount        = '3.33';
        $divisor       = '1.55';
        $dividedAmount = '2.14';
        $euro          = 'EUR';
        $subunits      = 2;

        $currency = Currency::create($euro, $subunits);
        $money    = Money::create($amount, $currency);

        $dividedMoney = $money->divide($divisor, LibMoney::ROUND_DOWN);

        Assert::assertInstanceOf(Money::class, $dividedMoney);
        Assert::assertSame($dividedAmount, $dividedMoney->getAmount());
        Assert::assertSame($euro, $dividedMoney->getCurrency()->getCode());
    }

    public function testDivideExtendedClassReturnsBaseClass() : void
    {
        $amount   = '3.33';
        $divisor  = '1.55';
        $euro     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($euro, $subunits);
        $money    = Money::create($amount, $currency);

        $dividedMoney = $money->divide($divisor);

        Assert::assertInstanceOf(Money::class, $dividedMoney);
    }

    public function testMod() : void
    {
        $amount          = '3.33';
        $divisorAmount   = '3.00';
        $remainingAmount = '0.33';
        $euro            = 'EUR';
        $subunits        = 2;

        $currency     = Currency::create($euro, $subunits);
        $money        = Money::create($amount, $currency);
        $divisorMoney = Money::create($divisorAmount, $currency);

        $remainingMoney = $money->mod($divisorMoney);

        Assert::assertInstanceOf(Money::class, $remainingMoney);
        Assert::assertSame($remainingAmount, $remainingMoney->getAmount());
        Assert::assertSame($euro, $remainingMoney->getCurrency()->getCode());
    }

    public function testModExtendedClassReturnsBaseClass() : void
    {
        $amount        = '3.33';
        $divisorAmount = '3.00';
        $euro          = 'EUR';
        $subunits      = 2;

        $currency     = Currency::create($euro, $subunits);
        $money        = ExtendedMoney::create($amount, $currency);
        $divisorMoney = Money::create($divisorAmount, $currency);

        $remainingMoney = $money->mod($divisorMoney);

        Assert::assertInstanceOf(Money::class, $remainingMoney);
    }

    public function testCantModOtherCurrencyMoney() : void
    {
        $amount          = '3.33';
        $divisorAmount   = '3.00';
        $remainingAmount = '0.33';
        $euro            = 'EUR';
        $dollar          = 'USD';
        $subunits        = 2;

        $euroCurrency   = Currency::create($euro, $subunits);
        $dollarCurrency = Currency::create($dollar, $subunits);
        $money          = Money::create($amount, $euroCurrency);
        $divisorMoney   = Money::create($divisorAmount, $dollarCurrency);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Currencies must be identical');

        $money->mod($divisorMoney);
    }

    public function testCantModOtherSubunitMoney() : void
    {
        $amount          = '3.33';
        $divisorAmount   = '3.00';
        $remainingAmount = '0.33';
        $euro            = 'EUR';
        $subunits        = 2;

        $currency     = Currency::create($euro, $subunits);
        $money        = Money::create($amount, $currency);
        $divisorMoney = GaapMoney::create($divisorAmount, $currency);

        $this->expectException(CannotWorkWithMoney::class);
        $this->expectExceptionMessage('Cannot execute method: mod on Money object: OnMoon\Money\Money with other Money object as argument: OnMoon\Money\GaapMoney. The classes have different subunits: 2 and 4.');

        $money->mod($divisorMoney);
    }

    public function testAllocate() : void
    {
        $amount                = '0.05';
        $ratios                = ['70', '30'];
        $firstAllocatedAmount  = '0.04';
        $secondAllocatedAmount = '0.01';
        $euro                  = 'EUR';
        $subunits              = 2;

        $currency = Currency::create($euro, $subunits);
        $money    = Money::create($amount, $currency);

        [$firstAllocatedMoney, $secondAllocatedMoney] = $money->allocate(...$ratios);

        Assert::assertInstanceOf(Money::class, $firstAllocatedMoney);
        Assert::assertSame($firstAllocatedAmount, $firstAllocatedMoney->getAmount());
        Assert::assertSame($euro, $firstAllocatedMoney->getCurrency()->getCode());
        Assert::assertInstanceOf(Money::class, $secondAllocatedMoney);
        Assert::assertSame($secondAllocatedAmount, $secondAllocatedMoney->getAmount());
        Assert::assertSame($euro, $secondAllocatedMoney->getCurrency()->getCode());
    }

    public function testAllocateExtendedClassReturnsBaseClass() : void
    {
        $amount   = '0.05';
        $ratios   = ['70', '30'];
        $euro     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($euro, $subunits);
        $money    = ExtendedMoney::create($amount, $currency);

        [$firstAllocatedMoney, $secondAllocatedMoney] = $money->allocate(...$ratios);

        Assert::assertInstanceOf(Money::class, $firstAllocatedMoney);
        Assert::assertInstanceOf(Money::class, $secondAllocatedMoney);
    }

    public function testAllocateTo() : void
    {
        $amount                = '8.00';
        $n                     = 3;
        $firstAllocatedAmount  = '2.67';
        $secondAllocatedAmount = '2.67';
        $thirdAllocatedAmount  = '2.66';
        $euro                  = 'EUR';
        $subunits              = 2;

        $currency = Currency::create($euro, $subunits);
        $money    = Money::create($amount, $currency);

        [$firstAllocatedMoney, $secondAllocatedMoney, $thirdAllocatedMoney] = $money->allocateTo($n);

        Assert::assertInstanceOf(Money::class, $firstAllocatedMoney);
        Assert::assertSame($firstAllocatedAmount, $firstAllocatedMoney->getAmount());
        Assert::assertSame($euro, $firstAllocatedMoney->getCurrency()->getCode());
        Assert::assertInstanceOf(Money::class, $secondAllocatedMoney);
        Assert::assertSame($secondAllocatedAmount, $secondAllocatedMoney->getAmount());
        Assert::assertSame($euro, $secondAllocatedMoney->getCurrency()->getCode());
        Assert::assertInstanceOf(Money::class, $thirdAllocatedMoney);
        Assert::assertSame($thirdAllocatedAmount, $thirdAllocatedMoney->getAmount());
        Assert::assertSame($euro, $thirdAllocatedMoney->getCurrency()->getCode());
    }

    public function testAllocateToExtendedClassReturnsBaseClass() : void
    {
        $amount   = '8.00';
        $n        = 3;
        $euro     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($euro, $subunits);
        $money    = ExtendedMoney::create($amount, $currency);

        [$firstAllocatedMoney, $secondAllocatedMoney, $thirdAllocatedMoney] = $money->allocateTo($n);

        Assert::assertInstanceOf(Money::class, $firstAllocatedMoney);
        Assert::assertInstanceOf(Money::class, $secondAllocatedMoney);
        Assert::assertInstanceOf(Money::class, $thirdAllocatedMoney);
    }

    public function testRatioOf() : void
    {
        $three         = '3.00';
        $six           = '6.00';
        $expectedRatio = '0.50000000000000';
        $euro          = 'EUR';
        $subunits      = 2;

        $currency   = Currency::create($euro, $subunits);
        $threeEuros = Money::create($three, $currency);
        $sixEuros   = Money::create($six, $currency);

        $ratio = $threeEuros->ratioOf($sixEuros);

        Assert::assertSame($expectedRatio, $ratio);
    }

    public function testCantRatioOfOtherSubunitMoney() : void
    {
        $three    = '3.00';
        $six      = '6.00';
        $euro     = 'EUR';
        $subunits = 2;

        $currency   = Currency::create($euro, $subunits);
        $threeEuros = Money::create($three, $currency);
        $sixEuros   = GaapMoney::create($six, $currency);

        $this->expectException(CannotWorkWithMoney::class);
        $this->expectExceptionMessage('Cannot execute method: ratioOf on Money object: OnMoon\Money\Money with other Money object as argument: OnMoon\Money\GaapMoney. The classes have different subunits: 2 and 4.');

        $threeEuros->ratioOf($sixEuros);
    }

    public function testAbsolute() : void
    {
        $amount         = '-3.00';
        $absoluteAmount = '3.00';
        $euro           = 'EUR';
        $subunits       = 2;

        $currency = Currency::create($euro, $subunits);
        $money    = Money::create($amount, $currency);

        $absoluteMoney = $money->absolute();

        Assert::assertInstanceOf(Money::class, $absoluteMoney);
        Assert::assertSame($absoluteAmount, $absoluteMoney->getAmount());
        Assert::assertSame($euro, $absoluteMoney->getCurrency()->getCode());
    }

    public function testAbsoluteExtendedClassReturnsBaseClass() : void
    {
        $amount   = '-3.00';
        $euro     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($euro, $subunits);
        $money    = ExtendedMoney::create($amount, $currency);

        $absoluteMoney = $money->absolute();

        Assert::assertInstanceOf(Money::class, $absoluteMoney);
    }

    public function testNegative() : void
    {
        $amount         = '3.00';
        $negativeAmount = '-3.00';
        $euro           = 'EUR';
        $subunits       = 2;

        $currency = Currency::create($euro, $subunits);
        $money    = Money::create($amount, $currency);

        $negativeMoney = $money->negative();

        Assert::assertInstanceOf(Money::class, $negativeMoney);
        Assert::assertSame($negativeAmount, $negativeMoney->getAmount());
        Assert::assertSame($euro, $negativeMoney->getCurrency()->getCode());
    }

    public function testNegativeExtendedClassReturnsBaseClass() : void
    {
        $amount   = '3.00';
        $euro     = 'EUR';
        $subunits = 2;

        $currency = Currency::create($euro, $subunits);
        $money    = ExtendedMoney::create($amount, $currency);

        $negativeMoney = $money->negative();

        Assert::assertInstanceOf(Money::class, $negativeMoney);
    }

    public function testIsZero() : void
    {
        $zeroAmount     = '0.00';
        $positiveAmount = '0.01';
        $negativeAmount = '-0.01';
        $euro           = 'EUR';
        $subunits       = 2;

        $currency            = Currency::create($euro, $subunits);
        $zeroAmountMoney     = Money::create($zeroAmount, $currency);
        $positiveAmountMoney = Money::create($positiveAmount, $currency);
        $negativeAmountMoney = Money::create($negativeAmount, $currency);

        Assert::assertTrue($zeroAmountMoney->isZero());
        Assert::assertFalse($positiveAmountMoney->isZero());
        Assert::assertFalse($negativeAmountMoney->isZero());
    }

    public function testIsPositive() : void
    {
        $zeroAmount     = '0.00';
        $positiveAmount = '0.01';
        $negativeAmount = '-0.01';
        $euro           = 'EUR';
        $subunits       = 2;

        $currency            = Currency::create($euro, $subunits);
        $zeroAmountMoney     = Money::create($zeroAmount, $currency);
        $positiveAmountMoney = Money::create($positiveAmount, $currency);
        $negativeAmountMoney = Money::create($negativeAmount, $currency);

        Assert::assertFalse($zeroAmountMoney->isPositive());
        Assert::assertTrue($positiveAmountMoney->isPositive());
        Assert::assertFalse($negativeAmountMoney->isPositive());
    }

    public function testIsNegative() : void
    {
        $zeroAmount     = '0.00';
        $positiveAmount = '0.01';
        $negativeAmount = '-0.01';
        $euro           = 'EUR';
        $subunits       = 2;

        $currency            = Currency::create($euro, $subunits);
        $zeroAmountMoney     = Money::create($zeroAmount, $currency);
        $positiveAmountMoney = Money::create($positiveAmount, $currency);
        $negativeAmountMoney = Money::create($negativeAmount, $currency);

        Assert::assertFalse($zeroAmountMoney->isNegative());
        Assert::assertFalse($positiveAmountMoney->isNegative());
        Assert::assertTrue($negativeAmountMoney->isNegative());
    }

    public function testJsonSerialize() : void
    {
        $amount             = '10.00';
        $euro               = 'EUR';
        $subunits           = 2;
        $expectedSerialized = [
            'amount' => $amount,
            'currency' => $euro,
        ];

        $currency = Currency::create($euro, $subunits);
        $money    = Money::create($amount, $currency);

        Assert::assertSame($expectedSerialized, $money->jsonSerialize());
    }

    public function testMin() : void
    {
        $ten      = '10.00';
        $fifteen  = '15.00';
        $euro     = 'EUR';
        $subunits = 2;

        $currency     = Currency::create($euro, $subunits);
        $tenEuros     = Money::create($ten, $currency);
        $fifteenEuros = Money::create($fifteen, $currency);

        Assert::assertSame($tenEuros, Money::min($tenEuros, $fifteenEuros));
    }

    public function testCantMinOtherCurrencyMoney() : void
    {
        $ten      = '10.00';
        $fifteen  = '15.00';
        $euro     = 'EUR';
        $dollar   = 'USD';
        $subunits = 2;

        $euroCurrency   = Currency::create($euro, $subunits);
        $dollarCurrency = Currency::create($dollar, $subunits);
        $tenEuros       = Money::create($ten, $euroCurrency);
        $fifteenDollars = Money::create($fifteen, $dollarCurrency);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Currencies must be identical');

        Money::min($tenEuros, $fifteenDollars);
    }

    public function testCantMinOtherSubunitMoney() : void
    {
        $ten      = '10.00';
        $fifteen  = '15.00';
        $euro     = 'EUR';
        $subunits = 2;

        $currency     = Currency::create($euro, $subunits);
        $tenEuros     = Money::create($ten, $currency);
        $fifteenEuros = GaapMoney::create($fifteen, $currency);

        $this->expectException(CannotWorkWithMoney::class);
        $this->expectExceptionMessage('Cannot execute method: min on Money object: OnMoon\Money\Money with other Money object as argument: OnMoon\Money\GaapMoney. The classes have different subunits: 2 and 4.');

        Money::min($tenEuros, $fifteenEuros);
    }

    public function testMax() : void
    {
        $ten      = '10.00';
        $fifteen  = '15.00';
        $euro     = 'EUR';
        $subunits = 2;

        $currency     = Currency::create($euro, $subunits);
        $tenEuros     = Money::create($ten, $currency);
        $fifteenEuros = Money::create($fifteen, $currency);

        Assert::assertSame($fifteenEuros, Money::max($tenEuros, $fifteenEuros));
    }

    public function testCantMaxOtherCurrencyMoney() : void
    {
        $ten      = '10.00';
        $fifteen  = '15.00';
        $euro     = 'EUR';
        $dollar   = 'USD';
        $subunits = 2;

        $euroCurrency   = Currency::create($euro, $subunits);
        $dollarCurrency = Currency::create($dollar, $subunits);
        $tenEuros       = Money::create($ten, $euroCurrency);
        $fifteenDollars = Money::create($fifteen, $dollarCurrency);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Currencies must be identical');

        Money::max($tenEuros, $fifteenDollars);
    }

    public function testCantMaxOtherSubunitMoney() : void
    {
        $ten      = '10.00';
        $fifteen  = '15.00';
        $euro     = 'EUR';
        $subunits = 2;

        $currency     = Currency::create($euro, $subunits);
        $tenEuros     = Money::create($ten, $currency);
        $fifteenEuros = GaapMoney::create($fifteen, $currency);

        $this->expectException(CannotWorkWithMoney::class);
        $this->expectExceptionMessage('Cannot execute method: max on Money object: OnMoon\Money\Money with other Money object as argument: OnMoon\Money\GaapMoney. The classes have different subunits: 2 and 4.');

        Money::max($tenEuros, $fifteenEuros);
    }

    public function testSum() : void
    {
        $ten        = '10.00';
        $fifteen    = '15.00';
        $twentyFive = '25.00';
        $euro       = 'EUR';
        $subunits   = 2;

        $currency     = Currency::create($euro, $subunits);
        $tenEuros     = Money::create($ten, $currency);
        $fifteenEuros = Money::create($fifteen, $currency);

        $moneySum = Money::sum($tenEuros, $fifteenEuros);

        Assert::assertInstanceOf(Money::class, $moneySum);
        Assert::assertSame($twentyFive, $moneySum->getAmount());
        Assert::assertSame($euro, $moneySum->getCurrency()->getCode());
    }

    public function testCantSumOtherCurrencyMoney() : void
    {
        $ten      = '10.00';
        $fifteen  = '15.00';
        $euro     = 'EUR';
        $dollar   = 'USD';
        $subunits = 2;

        $euroCurrency   = Currency::create($euro, $subunits);
        $dollarCurrency = Currency::create($dollar, $subunits);
        $tenEuros       = Money::create($ten, $euroCurrency);
        $fifteenDollars = Money::create($fifteen, $dollarCurrency);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Currencies must be identical');

        Money::sum($tenEuros, $fifteenDollars);
    }

    public function testCantSumOtherSubunitMoney() : void
    {
        $ten      = '10.00';
        $fifteen  = '15.00';
        $euro     = 'EUR';
        $subunits = 2;

        $currency     = Currency::create($euro, $subunits);
        $tenEuros     = Money::create($ten, $currency);
        $fifteenEuros = GaapMoney::create($fifteen, $currency);

        $this->expectException(CannotWorkWithMoney::class);
        $this->expectExceptionMessage('Cannot execute method: sum on Money object: OnMoon\Money\Money with other Money object as argument: OnMoon\Money\GaapMoney. The classes have different subunits: 2 and 4.');

        Money::sum($tenEuros, $fifteenEuros);
    }

    public function testAvg() : void
    {
        $ten      = '10.00';
        $twenty   = '20.00';
        $fifteen  = '15.00';
        $euro     = 'EUR';
        $subunits = 2;

        $currency    = Currency::create($euro, $subunits);
        $tenEuros    = Money::create($ten, $currency);
        $twentyEuros = Money::create($twenty, $currency);

        $avgMoney = Money::avg($tenEuros, $twentyEuros);

        Assert::assertInstanceOf(Money::class, $avgMoney);
        Assert::assertSame($fifteen, $avgMoney->getAmount());
        Assert::assertSame($euro, $avgMoney->getCurrency()->getCode());
    }

    public function testCantAvgOtherCurrencyMoney() : void
    {
        $ten      = '10.00';
        $fifteen  = '15.00';
        $euro     = 'EUR';
        $dollar   = 'USD';
        $subunits = 2;

        $euroCurrency   = Currency::create($euro, $subunits);
        $dollarCurrency = Currency::create($dollar, $subunits);
        $tenEuros       = Money::create($ten, $euroCurrency);
        $fifteenDollars = Money::create($fifteen, $dollarCurrency);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Currencies must be identical');

        Money::avg($tenEuros, $fifteenDollars);
    }

    public function testCantAvgOtherSubunitMoney() : void
    {
        $ten      = '10.00';
        $fifteen  = '15.00';
        $euro     = 'EUR';
        $subunits = 2;

        $currency     = Currency::create($euro, $subunits);
        $tenEuros     = Money::create($ten, $currency);
        $fifteenEuros = GaapMoney::create($fifteen, $currency);

        $this->expectException(CannotWorkWithMoney::class);
        $this->expectExceptionMessage('Cannot execute method: avg on Money object: OnMoon\Money\Money with other Money object as argument: OnMoon\Money\GaapMoney. The classes have different subunits: 2 and 4.');

        Money::avg($tenEuros, $fifteenEuros);
    }
}
