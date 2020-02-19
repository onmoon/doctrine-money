<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use OnMoon\Money\Type\MoneyType;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MoneyTypeTest extends TestCase
{
    public function testGetSQLDeclaration() : void
    {
        $type = Type::getType(MoneyType::TYPE_NAME);

        /** @var AbstractPlatform&MockObject $platformMock */
        $platformMock = $this->createMock(AbstractPlatform::class);
        $platformMock
            ->expects($this->once())
            ->method('getDecimalTypeDeclarationSQL')
            ->with(
                [
                    'precision' => 10,
                    'scale' => 2,
                ]
            )
            ->willReturn('');

        $type->getSQLDeclaration([], $platformMock);
    }

    public function testGetSQLDeclarationWithFieldDeclaration() : void
    {
        $type = Type::getType(MoneyType::TYPE_NAME);

        /** @var AbstractPlatform&MockObject $platformMock */
        $platformMock = $this->createMock(AbstractPlatform::class);
        $platformMock
            ->expects($this->once())
            ->method('getDecimalTypeDeclarationSQL')
            ->with(
                [
                    'something' => 11,
                    'precision' => 10,
                    'scale' => 2,
                ]
            )
            ->willReturn('');

        $type->getSQLDeclaration(
            [
                'something' => 11,
                'scale' => 1,
            ],
            $platformMock
        );
    }

    public function testConvertToPHPValue() : void
    {
        $expectedPhpValue = '10012';

        $type = Type::getType(MoneyType::TYPE_NAME);

        $databaseValue = '100.12';
        /** @var AbstractPlatform&MockObject $platformMock */
        $platformMock = $this->createMock(AbstractPlatform::class);

        $phpValue = $type->convertToPHPValue($databaseValue, $platformMock);

        Assert::assertSame($expectedPhpValue, $phpValue);
    }

    public function testConvertToDatabaseValue() : void
    {
        $expectedDatabaseValue = '100.12';

        $type = Type::getType(MoneyType::TYPE_NAME);

        $phpValue = '10012';
        /** @var AbstractPlatform&MockObject $platformMock */
        $platformMock = $this->createMock(AbstractPlatform::class);

        $phpValue = $type->convertToDatabaseValue($phpValue, $platformMock);

        Assert::assertSame($expectedDatabaseValue, $phpValue);
    }

    public function testGetName() : void
    {
        $type = Type::getType(MoneyType::TYPE_NAME);

        Assert::assertSame(MoneyType::TYPE_NAME, $type->getName());
    }
}
