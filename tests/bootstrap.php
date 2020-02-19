<?php

declare(strict_types=1);

use Doctrine\DBAL\Types\Type;
use OnMoon\Money\Type\BTCMoneyType;
use OnMoon\Money\Type\CurrencyType;
use OnMoon\Money\Type\GaapMoneyType;
use OnMoon\Money\Type\MoneyType;

error_reporting(E_ALL | E_STRICT);

require_once __DIR__ . '/../vendor/autoload.php';

Type::addType(BTCMoneyType::TYPE_NAME, BTCMoneyType::class);
Type::addType(GaapMoneyType::TYPE_NAME, GaapMoneyType::class);
Type::addType(MoneyType::TYPE_NAME, MoneyType::class);
Type::addType(CurrencyType::TYPE_NAME, CurrencyType::class);
