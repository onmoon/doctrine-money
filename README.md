# OnMoon Money

[![Latest Version](https://img.shields.io/github/v/release/onmoon/money)](https://github.com/onmoon/money/releases)
[![Build](https://github.com/onmoon/money/actions/workflows/ci.yml/badge.svg)](https://github.com/onmoon/money/actions)
[![License](https://img.shields.io/github/license/onmoon/money)](https://github.com/onmoon/money/blob/master/LICENSE)
[![Email](https://img.shields.io/badge/email-pf@csgo.com-blue.svg?style=flat-square)](mailto:pf@csgo.com)

OnMoon Money is an opinionated wrapper around MoneyPHP Money: https://github.com/moneyphp/money

- [Installation](#installation)
- [Features](#features)
- [Usage](#usage)
    - [Currency classes](#currency-classes)
    - [Money classes](#money-classes)
        - [Subunits](#subunits)
        - [Validation](#validation)
    - [Error handling](#error-handling)
    - [Using the library with Symfony and Doctrine](#using-the-library-with-symfony-and-doctrine)

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

```
composer require onmoon/money
```

## Features

On top of the wonderful API of the original, more strictness and some additional features are added.

**Money classes can be extended and used as Doctrine Embeddables**

The MoneyPHP objects are final, so you can't create your own domain value objects adding more semantics to the code:

```php
<?php

namespace App\Application\Service;

use Money\Money;

class InvoiceService
{
    public function calculateFee(Money $amount) : Money
    {
        ...
    }
}
```
With OnMoon Money you can do this:
```php
<?php

namespace App\Domain\Entity\Invoice\ValueObject;

use OnMoon\Money\Money;

class InvoiceAmount extends Money
{
}
```
```php
<?php

namespace App\Domain\Entity\Invoice\ValueObject;

use OnMoon\Money\Money;

class InvoiceFee extends Money
{
}
```
```php
<?php

namespace App\Application\Service;

use App\Domain\Entity\Invoice\ValueObject\InvoiceAmount;
use App\Domain\Entity\Invoice\ValueObject\InvoiceFee;

class InvoiceService
{
    public function calculateFee(InvoiceAmount $amount) : InvoiceFee
    {
        ...
    }
}
```
Also MoneyPHP Money class stores Currency internally as an object, that is a problem for mapping value objects
in Doctrine using embeddables, as the Money object is itself an embeddable and you get nested embeddables:
```php
<?php

namespace Money;

final class Money implements \JsonSerializable
{
    /**
     * @var Currency
     */
    private $currency;

    ...
}
```
OnMoon Money class stores currency internally as a string, and can be mapped as one embeddable using the provided
Doctrine Types:
```php
<?php

namespace OnMoon\Money;

abstract class BaseMoney
{
    /** @var string */
    private $amount;

    /** @var string */
    private $currency;

    ...
}
```
```xml
<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                          https://www.doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

    <embeddable name="My\Awesome\MoneyClass">
        <field name="amount" type="money" column="income" nullable="false" />
        <field name="currency" type="currency" column="income_currency" nullable="false" />
    </embeddable>
</doctrine-mapping>
```
**Money is created only from strings in strict formats depending on the currency**

MoneyPHP allows creating Money objects from a wide range of inputs and requires the input amount
to be in subunits of the currency. There is no check how many subunits the currency actually has.
This requires you to perform validation and checks in your code and can be error-prone.
```php
<?php

use Money\Money;
use Money\Currency;

$money = new Money(100, new Currency('EUR')); // 1 Euro
$money = new Money(100.00, new Currency('EUR')); // 1 Euro
$money = new Money('100', new Currency('EUR')); // 1 Euro
$money = new Money('100.00', new Currency('EUR')); // 1 Euro
$money = new Money('100.00', new Currency('XBT')); // 0.00000100 Bitcoins
```
OnMoon Money instead accepts ammounts only as strings containing the monetary amount in a
human-readable format and strictly enforces the format depending on the currency used.
```php
<?php

use OnMoon\Money\Money;
use OnMoon\Money\GaapMoney;
use OnMoon\Money\Bitcoin;
use OnMoon\Money\Currency;

$money = Money::create('100', Currency::create('BIF')); // 100 Burundi Francs
$money = Money::create('100.00', Currency::create('EUR')); // 100 Euros
$money = GaapMoney::create('100.000', Currency::create('IQD')); // 100 Iraqi Dinars
$money = Bitcoin::create('100.00000000', Currency::create('XBT')); // 100 Bitcoins

$money = Money::create(100, Currency::create('EUR')); // Error, invalid type
$money = Money::create(100.00, Currency::create('EUR')); // Error, invalid type
$money = Money::create('100', Currency::create('EUR')); // Error, no subunits specified
$money = Money::create('100.0', Currency::create('EUR')); // Error, not all subunits specified
$money = Money::create('100.000', Currency::create('EUR')); // Error, too many subunits specified
```
**The same API, but strictly typed**

MoneyPHP Money:

```
Money\Money::multiply($multiplier, $roundingMode = self::ROUND_HALF_UP)
Money\Money::allocate(array $ratios)
```

OnMoon Money:
```
OnMoon\Money\Money::multiply(string $multiplier, int $roundingMode = LibMoney::ROUND_UP) : self
OnMoon\Money\Money::allocate(string ...$ratios) : array
```

etc.

**Custom validation for your code extending the library classes with meaningful messages**
```php
<?php

namespace App\Domain\Entity\Invoice\ValueObject;

use Money\Currencies;
use Money\Currencies\CurrencyList;
use OnMoon\Money\BaseMoney;
use OnMoon\Money\Money;
use OnMoon\Money\Currency;
use OnMoon\Money\Exception\CannotCreateMoney;

class InvoiceIncome extends Money
{
    public static function humanReadableName() : string
    {
        return 'Invoice Income';
    }

    protected static function amountMustBeZeroOrGreater() : bool
    {
        return true;
    }

    protected static function getAllowedCurrencies() : Currencies
    {
        return new CurrencyList(['EUR' => 2, 'USD' => 2]);
    }

    protected static function validate(BaseMoney $money) : void
    {
        if ($money->getCurrency()->getCode() === 'EUR' &&
            $money->greaterThan(Money::create('50.00', $money->getCurrency()))
        ) {
            throw new CannotCreateMoney('Cannot exceed 50.00 for EUR currency');
        }   
    }
}

$invoiceIncome = InvoiceIncome::create('100.00', Currency::create('RUB')); // Error: Invalid Invoice Income with amount: 100.00 and currency: RUB. Currency not allowed.
$invoiceIncome = InvoiceIncome::create('100.00', Currency::create('EUR')); // Error: Cannot exceed 50.00 for EUR currency
$invoiceIncome = InvoiceIncome::create('-100.00', Currency::create('USD')); // Error: Invalid Invoice Income with amount: -100.00 and currency: USD. Amount must be zero or greater.
$invoiceIncome = InvoiceIncome::create('100.00', Currency::create('USD')); // ok
```
## Usage

For beginning, you should make yourself familiar with the [MoneyPHP Money documentation](http://moneyphp.org/en/stable/index.html)

### Currency classes

OnMoon Money provies a class for representing currency values: `OnMoon\Money\Currency`.

To create a currency object you will need the currency code:
```php
<?php

use OnMoon\Money\Currency;

$euroCode = 'EUR';

$euro = Currency::create($euroCode);
```
### Money classes

The API of a OnMoon Money class is the same as the MoneyPHP Money class:
- [Operations](http://moneyphp.org/en/stable/features/operation.html)
- [Comparison](http://moneyphp.org/en/stable/features/comparison.html)
- [Allocation](http://moneyphp.org/en/stable/features/allocation.html)

You can create your own Money classes with your own semantics:
```php
<?php

namespace App\Domain\Entity\Invoice\ValueObject;

use OnMoon\Money\Money;

class InvoiceAmount extends Money
{
}
```
You can create an instance of the specific Money class by using the named constructor:
```php
<?php

use OnMoon\Money\Money;
use OnMoon\Money\Currency;
use App\Domain\Entity\Invoice\ValueObject\InvoiceAmount;

$money = Money::create('100.00', Currency::create('EUR')); // instance of OnMoon\Money\Money
$money = InvoiceAmount::create('100.00', Currency::create('EUR')); // instance of App\Domain\Entity\Invoice\ValueObject\InvoiceAmount
```
#### Subunits

The library provies three base classes that you can use directly or extend from:

`OnMoon\Money\Money` - can work with currencies with up to 2 subunits

`OnMoon\Money\GaapMoney` - can work with currencies with up to 4 subunits and conforms with the [GAAP](https://en.wikipedia.org/wiki/Generally_Accepted_Accounting_Principles_\(United_States\)) standard

`OnMoon\Money\BTC` - can work with 8 subunits and is restricted to the Bitcoin (XBT) currency

Depending on the base class you use or extend from, some currencies may be unavailable due
to requiring more subunits than the base class can work with.

You should choose the base class depending on the currencies that your application will
use with the money class.
```php
<?php

namespace App\Domain\Entity\Invoice\ValueObject;

use OnMoon\Money\Money;
use OnMoon\Money\GaapMoney;
use OnMoon\Money\Currency;

class InvoiceAmount extends Money
{
}

class InvoiceFee extends GaapMoney
{
}

$money = InvoiceAmount::create('100.00', Currency::create('EUR')); // ok
$money = InvoiceAmount::create('100.000', Currency::create('BHD')); // error
$money = InvoiceFee::create('100.00', Currency::create('EUR')); // ok
$money = InvoiceFee::create('100.000', Currency::create('BHD')); // ok
```
If you need your own custom subunit amount you can extend any Money class and
implement the `classSubunits` method.
```php
<?php

namespace App\Domain\Entity\Invoice\ValueObject;

use OnMoon\Money\Money;
use OnMoon\Money\Currency;

class InvoiceAmount extends Money
{
    protected static function classSubunits() : int
    {
        return 0;
    }
}

$money = InvoiceAmount::create('100', Currency::create('DJF')); // ok
$money = InvoiceAmount::create('100.00', Currency::create('EUR')); // error
```
Remember, that you cannot use Money classes of different subunits in the Money class API:
```php
namespace App\Domain\Entity\Invoice\ValueObject;

use OnMoon\Money\Money;
use OnMoon\Money\Currency;

class TwoSubunitMoney extends Money
{
    protected static function subUnits() : int
    {
        return 2;
    }
}

class FourSubunitMoney extends Money
{
    protected static function subUnits() : int
    {
        return 4;
    }
}

$twoSubunitMoney = TwoSubunitMoney::create('100.00', Currency::create('EUR'));
$otherTwoSubunitMoney = TwoSubunitMoney::create('100.00', Currency::create('EUR'));

$twoSubunitMoney->add($otherTwoSubunitMoney); // ok

$fourSubunitMoney = FourSubunitMoney::create('100.00', Currency::create('EUR'));

$twoSubunitMoney->add($fourSubunitMoney); // error
```
#### Validation

On top of the validation provided by the base classes, you can enforce additional
constraints in your extended classes.

By implementing one of the following methods:
```php
<?php

namespace App\Domain\Entity\Invoice\ValueObject;

use OnMoon\Money\Money;
use OnMoon\Money\Currency;

class PositiveAmountMoney extends Money
{
    protected static function amountMustBeZeroOrGreater() : bool
    {
        return true;
    }
}

$money = PositiveAmountMoney::create('0.00', Currency::create('EUR')); // ok
$money = PositiveAmountMoney::create('-0.01', Currency::create('EUR')); // error

class GreaterThanZeroAmountMoney extends Money
{
    protected static function amountMustBeGreaterThanZero() : bool
    {
        return true;
    }
}

$money = GreaterThanZeroAmountMoney::create('0.01', Currency::create('EUR')); // ok
$money = GreaterThanZeroAmountMoney::create('0.00', Currency::create('EUR')); // error

class ZeroOrLessAmountMoney extends Money
{
    protected static function amountMustBeZeroOrLess() : bool
    {
        return true;
    }
}

$money = ZeroOrLessAmountMoney::create('0.00', Currency::create('EUR')); // ok
$money = ZeroOrLessAmountMoney::create('0.01', Currency::create('EUR')); // error

class NegativeAmountMoney extends Money
{
    protected static function amountMustBeLessThanZero() : bool
    {
        return true;
    }
}

$money = NegativeAmountMoney::create('-0.01', Currency::create('EUR')); // ok
$money = NegativeAmountMoney::create('0.00', Currency::create('EUR')); // error
```
If you need more complex validation logic, implement the following method:
```php
<?php

namespace App\Domain\Entity\Invoice\ValueObject;

use OnMoon\Money\BaseMoney;
use OnMoon\Money\Exception\CannotCreateMoney;
use OnMoon\Money\Money;
use OnMoon\Money\Currency;

class ComplexValidationMoney extends Money
{
    protected static function validate(BaseMoney $money) : void
    {
        if ($money->getCurrency()->getCode() === 'EUR' &&
            $money->greaterThan(Money::create('50.00', $money->getCurrency()))
        ) {
            throw new CannotCreateMoney('Cannot work with Euros if amount is greater than 50.00');
        }   
    }
}

$money = ComplexValidationMoney::create('40.00', Currency::create('EUR')); // ok
$money = ComplexValidationMoney::create('51.00', Currency::create('EUR')); // error
```
You can also specify the list of currencies that are allowed for a Money class and all classes that extend from it:
```php
<?php

namespace App\Domain\Entity\Invoice\ValueObject;

use Money\Currencies;
use Money\Currencies\CurrencyList;
use OnMoon\Money\BaseMoney;
use OnMoon\Money\Exception\CannotCreateMoney;
use OnMoon\Money\Money;
use OnMoon\Money\Currency;

class OnlyUsdMoney extends Money
{
    protected static function getAllowedCurrencies() : Currencies
    {
        return new CurrencyList(['USD' => 2]);
    }
}

$money = OnlyUsdMoney::create('50.00', Currency::create('USD')); // ok
$money = OnlyUsdMoney::create('50.00', Currency::create('EUR')); // error
```
The default classes provided by the library support the following currencies:

`OnMoon\Money\Money` - All ISO currencies with 0-2 subunits

`OnMoon\Money\GaapMoney` - All ISO currencies with 0-4 subunits

`OnMoon\Money\BTC` - Only XBT with 8 subunits

All operations on the Money class that change the amount will return the base class instead of the extended,
as the resulting amount can violate the invariants of the extended class:
```php
<?php

namespace App\Domain\Entity\Invoice\ValueObject;

use OnMoon\Money\Money;
use OnMoon\Money\Currency;

class MyMoney extends Money
{
    protected static function amountMustBeZeroOrGreater() : bool
    {
        return true;
    }
}

$money      = MyMoney::create('100.00', Currency::create('EUR')); // instance of App\Domain\Entity\Invoice\ValueObject\MyMoney
$otherMoney = MyMoney::create('200.00', Currency::create('EUR')); // instance of App\Domain\Entity\Invoice\ValueObject\MyMoney

$sum = $money->subtract($otherMoney); // returns instance of OnMoon\Money\Money
```
### Error handling

Exceptions thrown by OnMoon money classes are extended from two base exceptions:

- `OnMoon\Money\Exception\MoneyLogicError` - Errors that represent a logic error in your code and should be 
avoided in production, error messages should not be shown to the user.
- `OnMoon\Money\Exception\MoneyRuntimeError` - Errors that represent a runtime error in your code and can depend on user input. 
You can use them safely to display errors to the user.

Examples of `OnMoon\Money\Exception\MoneyRuntimeError` error messages:
- Invalid Money with amount: 100.00 and currency: RUB. Currency not allowed.
- Invalid Money with amount: 50.000 and currency: EUR. Invalid amount format. The correct format is: /^-?\d+\.\d{2}$/.
- Invalid Money with amount: -11.00 and currency: USD. Amount must be greater than zero.

You can make theese messages even more helpful, by implementing the `humanReadableName` method in your Money Classes:
```php
<?php

namespace App\Domain\Entity\Transaction\ValueObject;

use OnMoon\Money\Money;

class TransactionFee extends Money
{
    public static function humanReadableName() : string
    {
        return 'Transaction Fee';
    }
}
```
The error messages then will look like this:
- Invalid Transaction Fee with amount: 100.00 and currency: RUB. Currency not allowed.
- Invalid Transaction Fee with amount: 50.000 and currency: EUR. Invalid amount format. The correct format is: /^-?\d+\.\d{2}$/.
- Invalid Transaction Fee with amount: -11.00 and currency: USD. Amount must be greater than zero.

If you want to catch all exceptions thrown by OnMoon Money, including the exceptions of the 
underlying MoneyPHP Money code - use the `Money\Exception` interface.

### Using the library with Symfony and Doctrine

The library provides four Doctrine types to persist the Money and Currency objects to the database:

- `OnMoon\Money\Type\BTCMoneyType` - Should be used only for classes extending `OnMoon\Money\BTC`
- `OnMoon\Money\Type\GaapMoneyType` - Should be used only for classes extending `OnMoon\Money\GaapMoney`
- `OnMoon\Money\Type\MoneyType` - Should be used only for classes extending `OnMoon\Money\Money`
- `OnMoon\Money\Type\CurrencyType` - Should be used only for classes extending `OnMoon\Money\Curency`

The rule of thumb for Type classes mapping the Money object is that the Type class decimal precision
should be equal to the Money class subunits.  If they will be different, you will get other amounts 
from the database than previously saved.

#### Example code

Entity:
```php
<?php

namespace App\Domain\Entity\Invoice;

use App\Domain\Entity\Invoice\ValueObject\InvoiceIncome;

class Invoice
{
    /** @var InvoiceIncome $income */
    private $income;

    public function __construct(InvoiceIncome $income)
    {
        $this->income = $income;
    }
    
    public function income() : InvoiceIncome
    {
        return $this->income();    
    }
}
```
Value object:
```php
<?php

namespace App\Domain\Entity\Invoice\ValueObject;

use OnMoon\Money\Money;

class InvoiceIncome extends Money
{
}
```
/config/packages/doctrine.xml:
```xml
<?xml version="1.0" encoding="UTF-8" ?>
<container xmlns="http://symfony.com/schema/dic/services"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xmlns:doctrine="http://symfony.com/schema/dic/doctrine"
           xsi:schemaLocation="http://symfony.com/schema/dic/services
        http://symfony.com/schema/dic/services/services-1.0.xsd
        http://symfony.com/schema/dic/doctrine
        http://symfony.com/schema/dic/doctrine/doctrine-1.0.xsd">

    <doctrine:config>
        <doctrine:dbal>
            <doctrine:type name="money">OnMoon\Money\Type\MoneyType</doctrine:type>
            <doctrine:type name="currency">OnMoon\Money\Type\CurrencyType</doctrine:type>
        </doctrine:dbal>
    </doctrine:config>
</container>
```
Entity mapping:
```xml
<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                          https://www.doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

    <entity name="App\Domain\Entity\Invoice\Invoice" table="invoices">
        <embedded name="income" class="App\Domain\Entity\Invoice\ValueObject\InvoiceIncome" use-column-prefix="false" />
    </entity>
</doctrine-mapping>
```
Value object mapping:
```xml
<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                          https://www.doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

    <embeddable name="App\Domain\Entity\Invoice\ValueObject\InvoiceIncome">
        <field name="amount" type="money" column="income" nullable="false" />
        <field name="currency" type="currency" column="income_currency" nullable="false" />
    </embeddable>
</doctrine-mapping>
```