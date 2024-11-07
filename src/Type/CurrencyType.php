<?php

declare(strict_types=1);

namespace OnMoon\Money\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

use function array_merge;

class CurrencyType extends Type
{
    public const TYPE_NAME = 'currency';

    /** @param mixed[] $column */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getVarcharTypeDeclarationSQL(
            array_merge($column, ['length' => 3]),
        );
    }

    public function getName(): string
    {
        return self::TYPE_NAME;
    }
}
