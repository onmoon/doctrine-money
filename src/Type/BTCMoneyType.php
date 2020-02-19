<?php

declare(strict_types=1);

namespace OnMoon\Money\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use OnMoon\Money\BTC;
use function array_merge;

class BTCMoneyType extends Type
{
    public const TYPE_NAME = 'btc_money';

    /**
     * @param mixed[] $fieldDeclaration
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform) : string
    {
        return $platform->getDecimalTypeDeclarationSQL(
            array_merge(
                $fieldDeclaration,
                [
                    'precision' => 16,
                    'scale' => 8,
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
        return BTC::toSubunits((string) $value);
    }

    /**
     * @param mixed $value
     *
     * phpcs:disable SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform) : string
    {
        return BTC::fromSubunits((string) $value);
    }

    public function getName() : string
    {
        return self::TYPE_NAME;
    }
}
