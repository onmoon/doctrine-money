<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use OnMoon\Money\Type\BTCMoneyType;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BTCMoneyTypeTest extends TestCase
{
    public function testGetSQLDeclaration(): void
    {
        $type = Type::getType(BTCMoneyType::TYPE_NAME);

        /** @var AbstractPlatform&MockObject $platformMock */
        $platformMock = $this->createMock(AbstractPlatform::class);
        $platformMock
            ->expects($this->once())
            ->method('getDecimalTypeDeclarationSQL')
            ->with(
                [
                    'precision' => 16,
                    'scale' => 8,
                ],
            )
            ->willReturn('');

        $type->getSQLDeclaration([], $platformMock);
    }

    public function testGetSQLDeclarationWithFieldDeclaration(): void
    {
        $type = Type::getType(BTCMoneyType::TYPE_NAME);

        /** @var AbstractPlatform&MockObject $platformMock */
        $platformMock = $this->createMock(AbstractPlatform::class);
        $platformMock
            ->expects($this->once())
            ->method('getDecimalTypeDeclarationSQL')
            ->with(
                [
                    'something' => 10,
                    'precision' => 16,
                    'scale' => 8,
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
        $expectedPhpValue = '10012345678';

        $type = Type::getType(BTCMoneyType::TYPE_NAME);

        $databaseValue = '100.12345678';
        /** @var AbstractPlatform&MockObject $platformMock */
        $platformMock = $this->createMock(AbstractPlatform::class);

        $phpValue = $type->convertToPHPValue($databaseValue, $platformMock);

        Assert::assertSame($expectedPhpValue, $phpValue);
    }

    public function testConvertToNullPHPValue(): void
    {
        $expectedPhpValue = null;

        $type = Type::getType(BTCMoneyType::TYPE_NAME);

        $databaseValue = null;
        /** @var AbstractPlatform&MockObject $platformMock */
        $platformMock = $this->createMock(AbstractPlatform::class);

        $phpValue = $type->convertToPHPValue($databaseValue, $platformMock);

        Assert::assertSame($expectedPhpValue, $phpValue);
    }

    public function testConvertToDatabaseValue(): void
    {
        $expectedDatabaseValue = '100.12345678';

        $type = Type::getType(BTCMoneyType::TYPE_NAME);

        $phpValue = '10012345678';
        /** @var AbstractPlatform&MockObject $platformMock */
        $platformMock = $this->createMock(AbstractPlatform::class);

        $phpValue = $type->convertToDatabaseValue($phpValue, $platformMock);

        Assert::assertSame($expectedDatabaseValue, $phpValue);
    }

    public function testConvertToNullDatabaseValue(): void
    {
        $expectedDatabaseValue = null;

        $type = Type::getType(BTCMoneyType::TYPE_NAME);

        $phpValue = null;
        /** @var AbstractPlatform&MockObject $platformMock */
        $platformMock = $this->createMock(AbstractPlatform::class);

        $phpValue = $type->convertToDatabaseValue($phpValue, $platformMock);

        Assert::assertSame($expectedDatabaseValue, $phpValue);
    }

    public function testGetName(): void
    {
        $type = Type::getType(BTCMoneyType::TYPE_NAME);

        Assert::assertSame(BTCMoneyType::TYPE_NAME, $type->getName());
    }
}
