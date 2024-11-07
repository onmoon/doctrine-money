<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use OnMoon\Money\Type\GaapMoneyType;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GaapMoneyTypeTest extends TestCase
{
    public function testGetSQLDeclaration(): void
    {
        $type = Type::getType(GaapMoneyType::TYPE_NAME);

        /** @var AbstractPlatform&MockObject $platformMock */
        $platformMock = $this->createMock(AbstractPlatform::class);
        $platformMock
            ->expects($this->once())
            ->method('getDecimalTypeDeclarationSQL')
            ->with(
                [
                    'precision' => 15,
                    'scale' => 4,
                ],
            )
            ->willReturn('');

        $type->getSQLDeclaration([], $platformMock);
    }

    public function testGetSQLDeclarationWithFieldDeclaration(): void
    {
        $type = Type::getType(GaapMoneyType::TYPE_NAME);

        /** @var AbstractPlatform&MockObject $platformMock */
        $platformMock = $this->createMock(AbstractPlatform::class);
        $platformMock
            ->expects($this->once())
            ->method('getDecimalTypeDeclarationSQL')
            ->with(
                [
                    'something' => 10,
                    'precision' => 15,
                    'scale' => 4,
                ],
            )
            ->willReturn('');

        $type->getSQLDeclaration(
            [
                'something' => 10,
                'scale' => 2,
            ],
            $platformMock,
        );
    }

    public function testConvertToPHPValue(): void
    {
        $expectedPhpValue = '1001234';

        $type = Type::getType(GaapMoneyType::TYPE_NAME);

        $databaseValue = '100.1234';
        /** @var AbstractPlatform&MockObject $platformMock */
        $platformMock = $this->createMock(AbstractPlatform::class);

        $phpValue = $type->convertToPHPValue($databaseValue, $platformMock);

        Assert::assertSame($expectedPhpValue, $phpValue);
    }

    public function testConvertToNullPHPValue(): void
    {
        $expectedPhpValue = null;

        $type = Type::getType(GaapMoneyType::TYPE_NAME);

        $databaseValue = null;
        /** @var AbstractPlatform&MockObject $platformMock */
        $platformMock = $this->createMock(AbstractPlatform::class);

        $phpValue = $type->convertToPHPValue($databaseValue, $platformMock);

        Assert::assertSame($expectedPhpValue, $phpValue);
    }

    public function testConvertToDatabaseValue(): void
    {
        $expectedDatabaseValue = '100.1234';

        $type = Type::getType(GaapMoneyType::TYPE_NAME);

        $phpValue = '1001234';
        /** @var AbstractPlatform&MockObject $platformMock */
        $platformMock = $this->createMock(AbstractPlatform::class);

        $phpValue = $type->convertToDatabaseValue($phpValue, $platformMock);

        Assert::assertSame($expectedDatabaseValue, $phpValue);
    }

    public function testConvertToNullDatabaseValue(): void
    {
        $expectedDatabaseValue = null;

        $type = Type::getType(GaapMoneyType::TYPE_NAME);

        $phpValue = null;
        /** @var AbstractPlatform&MockObject $platformMock */
        $platformMock = $this->createMock(AbstractPlatform::class);

        $phpValue = $type->convertToDatabaseValue($phpValue, $platformMock);

        Assert::assertSame($expectedDatabaseValue, $phpValue);
    }

    public function testGetName(): void
    {
        $type = Type::getType(GaapMoneyType::TYPE_NAME);

        Assert::assertSame(GaapMoneyType::TYPE_NAME, $type->getName());
    }
}
