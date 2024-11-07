<?php

declare(strict_types=1);

namespace OnMoon\Money\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

use function array_merge;
use function bcdiv;
use function bcmul;

class BTCMoneyType extends Type
{
    public const TYPE_NAME = 'btc_money';

    /** @param mixed[] $column */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDecimalTypeDeclarationSQL(
            array_merge(
                $column,
                [
                    'precision' => 16,
                    'scale' => 8,
                ],
            ),
        );
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): string|null
    {
        if ($value === null) {
            return null;
        }

        /** @psalm-var numeric-string $value */
        $value = (string) $value;

        return bcmul($value, '100000000', 0);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): string|null
    {
        if ($value === null) {
            return null;
        }

        /** @psalm-var numeric-string $value */
        $value = (string) $value;

        return bcdiv($value, '100000000', 8);
    }

    public function getName(): string
    {
        return self::TYPE_NAME;
    }
}
