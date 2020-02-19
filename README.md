# OnMoon Money

This library is an opinionated wrapper around https://github.com/moneyphp/money

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

```
composer require onmoon/money
```

## Features

On top of the wonderful API of the original, more strictness and some additional features are added.

**Money classes can be extended**

The original library objects are final, so you can't create your own domain value objects adding more semantics to the code.

Instead of:
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
You can do this:
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

**Money is created only from strings in whole units**

The original library allows creating Money objects from a wide range of inputs and requires them to
represent the subunits of a currency. There is no check how many subunits the currency actually has.
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

This extension instead accepts ammounts only as strings containing the monetary amount in a
human-readable format and strictly enforces the format depending on the currency used.

```php
<?php

use OnMoon\Money\Money;
use OnMoon\Money\BTC;
use OnMoon\Money\Currency;

$money = Money::create(100, Currency::create('EUR', 2)); // Error
$money = Money::create(100.00, Currency::create('EUR', 2)); // Error
$money = Money::create('100', Currency::create('EUR', 2)); // Error
$money = Money::create('100.0', Currency::create('EUR', 2)); // Error
$money = Money::create('100.00', Currency::create('EUR', 2)); // 100 Euros
$money = Money::create('100.000', Currency::create('EUR', 2)); // Error
$money = BTC::create('100.00', Currency::create('XBT', 8)); // Error
$money = BTC::create('100.00000000', Currency::create('XBT', 8)); // 100 Bitcoins
```

**The same API, but strictly typed**

Original library:

```
Money\Money::multiply($multiplier, $roundingMode = self::ROUND_HALF_UP)
Money\Money::allocate(array $ratios)
```

The extension:
```
GameMoney\Money\Money::multiply(string $multiplier, int $roundingMode = LibMoney::ROUND_UP) : self
GameMoney\Money\Money::allocate(string ...$ratios) : array
```

etc.

**Custom validation for your code extending the library classes with meaningful messages**

```php
<?php

namespace App\Domain\Entity\Invoice\ValueObject;

use OnMoon\Money\Money;
use OnMoon\Money\Currency;
use OnMoon\Money\Exception\CannotCreateMoney;

class InvoiceIncome extends Money
{
    protected static function humanReadableName() : string
    {
        return 'Invoice Income';
    }

    protected static function amountMustBeZeroOrGreater() : bool
    {
        return true;
    }

    protected static function checkAmount(self $money) : void
    {
        if ($money->getCurrency()->getCode() === 'EUR') {
            throw new CannotCreateMoney('Invoices do not accept Euros');
        }   
    }
}

$invoiceIncome = InvoiceIncome::create('100.00', Currency::create('EUR')); // Error: Invoices do not accept Euros
$invoiceIncome = InvoiceIncome::create('-100.00', Currency::create('USD')); // Error: Cannot create Invoice Income from amount: -100.00 - amount must be zero or greater.
$invoiceIncome = InvoiceIncome::create('100.00', Currency::create('USD')); // ok
```

**Factories that know what currencies are allowed in what contexts of your application**

```php
<?php

use App\Domain\Entity\Invoice\ValueObject\InvoiceIncome;
use App\Domain\Entity\Checkout\ValueObject\CheckoutFee;
use Money\Currencies\CurrencyList;
use OnMoon\Money\Factory\CurrencyFactory;
use OnMoon\Money\Factory\MoneyFactory;
use OnMoon\Money\Money;

$invoiceCurrencies = ['EUR' => 2, 'USD' => 2];
$checkoutCurrencies = ['RUB' => 2];

$currencyFactory = new CurrencyFactory();
$currencyFactory->registerContext('invoice', new CurrencyList($invoiceCurrencies));
$currencyFactory->registerContext('checkout', new CurrencyList($checkoutCurrencies));

$moneyFactory = new MoneyFactory($currencyFactory);

$money = $moneyFactory->create('100.00', 'EUR', InvoiceIncome::class, 'invoice'); // ok
$money = $moneyFactory->create('100.00', 'EUR', CheckoutFee::class, 'checkout'); // error
```

## Usage

For beginning, you should make yourself familiar with the [original library's documentation](http://moneyphp.org/en/stable/index.html)

### Currency classes

The library provies a class for representing currency values `OnMoon\Money\Currency`

To create a currency object you will need it's currency code and the number of subunits the currency has:

```php
<?php

use OnMoon\Money\Currency;

$euroCode     = 'EUR';
$euroSubunits = 2;

$euro = Currency::create($euroCode, $euroSubunits);
```

### Money classes

The API of a Money class is the same as the original library's Money class:
- [Operations](http://moneyphp.org/en/stable/features/operation.html)
- [Comparison](http://moneyphp.org/en/stable/features/comparison.html)
- [Allocation](http://moneyphp.org/en/stable/features/allocation.html)

You can create your own classes with your own semantics:

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

$money = Money::create('100.00', Currency::create('EUR', 2)); // instance of OnMoon\Money\Money
$money = InvoiceAmount::create('100.00', Currency::create('EUR', 2)); // instance of App\Domain\Entity\Invoice\ValueObject\InvoiceAmount
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


$money = InvoiceAmount::create('100.00', Currency::create('EUR', 2)); // ok
$money = InvoiceAmount::create('100.000', Currency::create('BHD', 3)); // error
$money = InvoiceFee::create('100.00', Currency::create('EUR', 2)); // ok
$money = InvoiceFee::create('100.000', Currency::create('BHD', 3)); // ok
```

If you need your own custom subunit amount you can extend the `OnMoon\Money\BaseMoney` base class and
implement this method:

```php
<?php

namespace App\Domain\Entity\Invoice\ValueObject;

use OnMoon\Money\BaseMoney;
use OnMoon\Money\Currency;

class InvoiceAmount extends BaseMoney
{
    protected static function subUnits() : int
    {
        return 0;
    }
}

$money = InvoiceAmount::create('100', Currency::create('DJF', 0)); // ok
$money = InvoiceAmount::create('100.00', Currency::create('EUR', 2)); // error
```

Remember, that you cannot use Money classes of different subunits in the Money class API:

```php
namespace App\Domain\Entity\Invoice\ValueObject;

use OnMoon\Money\BaseMoney;
use OnMoon\Money\Currency;

class TwoSubunitMoney extends BaseMoney
{
    protected static function subUnits() : int
    {
        return 2;
    }
}

class FourSubunitMoney extends BaseMoney
{
    protected static function subUnits() : int
    {
        return 4;
    }
}

$twoSubunitMoney = TwoSubunitMoney::create('100.00', Currency::create('EUR', 2));
$otherTwoSubunitMoney = TwoSubunitMoney::create('100.00', Currency::create('EUR', 2));

$twoSubunitMoney->add($otherTwoSubunitMoney); // ok

$fourSubunitMoney = FourSubunitMoney::create('100.0000', Currency::create('EUR', 4));

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

$money = PositiveAmountMoney::create('0.00', Currency::create('EUR', 2)); // ok
$money = PositiveAmountMoney::create('-0.01', Currency::create('EUR', 2)); // error

class GreaterThanZeroAmountMoney extends Money
{
    protected static function amountMustBeGreaterThanZero() : bool
    {
        return true;
    }
}

$money = GreaterThanZeroAmountMoney::create('0.01', Currency::create('EUR', 2)); // ok
$money = GreaterThanZeroAmountMoney::create('0.00', Currency::create('EUR', 2)); // error

class ZeroOrLessAmountMoney extends Money
{
    protected static function amountMustBeZeroOrLess() : bool
    {
        return true;
    }
}

$money = ZeroOrLessAmountMoney::create('0.00', Currency::create('EUR', 2)); // ok
$money = ZeroOrLessAmountMoney::create('0.01', Currency::create('EUR', 2)); // error

class NegativeAmountMoney extends Money
{
    protected static function amountMustBeLessThanZero() : bool
    {
        return true;
    }
}

$money = NegativeAmountMoney::create('-0.01', Currency::create('EUR', 2)); // ok
$money = NegativeAmountMoney::create('0.00', Currency::create('EUR', 2)); // error
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

$money = ComplexValidationMoney::create('40.00', Currency::create('EUR', 2)); // ok
$money = ComplexValidationMoney::create('51.00', Currency::create('EUR', 2)); // error
```

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

$money      = MyMoney::create('100.00', Currency::create('EUR', 2)); // instance of App\Domain\Entity\Invoice\ValueObject\MyMoney
$otherMoney = MyMoney::create('200.00', Currency::create('EUR', 2)); // instance of App\Domain\Entity\Invoice\ValueObject\MyMoney

$sum = $money->subtract($otherMoney); // returns instance of OnMoon\Money\Money
```

### Factories

#### Creating currencies

To ease the creating of currency classes, you can use the provided factory.

The currency factory works with the [`Money\Currencies`](http://moneyphp.org/en/stable/features/currencies.html) 
interface of the original library and accepts any of it's implementations from it:
- `\Money\Currencies\AggregateCurrencies`
- `\Money\Currencies\BitcoinCurrencies`
- `\Money\Currencies\CachedCurrencies`
- `\Money\Currencies\CurrencyList`
- `\Money\Currencies\ISOCurrencies`

```php
<?php

use Money\Currencies\CurrencyList;
use Money\Currencies\ISOCurrencies;use OnMoon\Money\Factory\CurrencyFactory;
use OnMoon\Money\Factory\MoneyFactory;
use OnMoon\Money\Money;

$currencies      = new ISOCurrencies();
$currencyFactory = new CurrencyFactory($currencies);

$currency = $currencyFactory->create('EUR'); // ok
```

The factory will throw an exception, if the requested currency is not present in the provied `Money\Currencies` implementation:

```php
<?php

use Money\Currencies\CurrencyList;
use Money\Currencies\ISOCurrencies;use OnMoon\Money\Factory\CurrencyFactory;
use OnMoon\Money\Factory\MoneyFactory;
use OnMoon\Money\Money;

$currencies      = new CurrencyList(['EUR' => 2]);
$currencyFactory = new CurrencyFactory($currencies);

$currency = $currencyFactory->create('USD'); // error
```

You can have different sets of currencies allowed in different parts of your application, to enforce theese restrictions
you can register Currencies belonging to a specific context in your application.

```php
<?php

use Money\Currencies\CurrencyList;
use OnMoon\Money\Factory\CurrencyFactory;

$invoiceCurrencies = ['EUR' => 2, 'USD' => 2];
$checkoutCurrencies = ['RUB' => 2];

$currencyFactory = new CurrencyFactory();
$currencyFactory->registerContext('invoice', new CurrencyList($invoiceCurrencies));
$currencyFactory->registerContext('checkout', new CurrencyList($checkoutCurrencies));

$money = $currencyFactory->create('EUR', 'invoice'); // ok
$money = $currencyFactory->create('EUR', 'checkout'); // error
```

#### Creating money

To ease the creating of money classes, you can use the provided factory.

```php
<?php

use Money\Currencies\CurrencyList;
use OnMoon\Money\Factory\CurrencyFactory;
use OnMoon\Money\Factory\MoneyFactory;
use OnMoon\Money\Money;

$currencies = ['EUR' => 2, 'USD' => 2];

$currencyFactory = new CurrencyFactory(new CurrencyList($currencies));
$moneyFactory    = new MoneyFactory($currencyFactory);

$money = $moneyFactory->create('100.00', 'EUR', Money::class);
```

A money factory uses a currency factory internally and can enforce the same restrictions as the currency factory:

```php
<?php

use Money\Currencies\CurrencyList;
use OnMoon\Money\Factory\CurrencyFactory;
use OnMoon\Money\Factory\MoneyFactory;
use OnMoon\Money\Money;

$invoiceCurrencies = ['EUR' => 2, 'USD' => 2];
$checkoutCurrencies = ['RUB' => 2];

$currencyFactory = new CurrencyFactory();
$currencyFactory->registerContext('invoice', new CurrencyList($invoiceCurrencies));
$currencyFactory->registerContext('checkout', new CurrencyList($checkoutCurrencies));

$moneyFactory = new MoneyFactory($currencyFactory);

$money = $moneyFactory->create('100.00', 'EUR', Money::class, 'invoice'); // ok
$money = $moneyFactory->create('100.00', 'EUR', Money::class, 'checkout'); // error
```

### Error handling
### Using the library with Symfony and Doctrine

The library provides four Doctrine types to persist the Money and Currency objects to the database:

- `OnMoon\Money\Type\BTCMoneyType` - Should be used only for classes extending `OnMoon\Money\BTC`
- `OnMoon\Money\Type\GaapMoneyType` - Should be used only for classes extending `OnMoon\Money\GaapMoney`
- `OnMoon\Money\Type\MoneyType` - Should be used only for classes extending `OnMoon\Money\Money`
- `OnMoon\Money\Type\CurrencyType` - Should be used only for classes extending `OnMoon\Money\Curency`

The rule of thumb for Type classes mapping the Money object is that the Type class decimal precision
should be equal to the Money class subunits.  If they will be different, you will get other amounts 
from the database than you previously saved.

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