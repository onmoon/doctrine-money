<?php

declare(strict_types=1);

namespace OnMoon\Money\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use OnMoon\Money\Money;
use function array_merge;

class MoneyType extends Type
{
    public const TYPE_NAME = 'money';

    /**
     * @param mixed[] $fieldDeclaration
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform) : string
    {
        return $platform->getDecimalTypeDeclarationSQL(
            array_merge(
                $fieldDeclaration,
                [
                    'precision' => 10,
                    'scale' => 2,
                ]
            )
        );
    }

    /**
     * @param mixed $value
     *
     * phpcs:disable SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     */
    public function convertToPHPValue($value, AbstractPlatform $platform) : string
    {
        return Money::toSubunits((string) $value);
    }

    /**
     * @param mixed $value
     *
     * phpcs:disable SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform) : string
    {
        return Money::fromSubunits((string) $value);
    }

    public function getName() : string
    {
        return self::TYPE_NAME;
    }
}
