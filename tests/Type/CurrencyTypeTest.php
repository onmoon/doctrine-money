<?php

declare(strict_types=1);

namespace OnMoon\Money\Tests\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use OnMoon\Money\Type\CurrencyType;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CurrencyTypeTest extends TestCase
{
    public function testGetSQLDeclaration() : void
    {
        $type = Type::getType(CurrencyType::TYPE_NAME);

        /** @var AbstractPlatform&MockObject $platformMock */
        $platformMock = $this->createMock(AbstractPlatform::class);
        $platformMock
            ->expects($this->once())
            ->method('getVarcharTypeDeclarationSQL')
            ->with(['length' => 3])
            ->willReturn('');

        $type->getSQLDeclaration([], $platformMock);
    }

    public function testGetSQLDeclarationWithFieldDeclaration() : void
    {
        $type = Type::getType(CurrencyType::TYPE_NAME);

        /** @var AbstractPlatform&MockObject $platformMock */
        $platformMock = $this->createMock(AbstractPlatform::class);
        $platformMock
            ->expects($this->once())
            ->method('getVarcharTypeDeclarationSQL')
            ->with(
                [
                    'something' => 10,
                    'length' => 3,
                ]
            )
            ->willReturn('');

        $type->getSQLDeclaration(
            [
                'something' => 10,
                'length' => 2,
            ],
            $platformMock
        );
    }

    public function testGetName() : void
    {
        $type = Type::getType(CurrencyType::TYPE_NAME);

        Assert::assertSame(CurrencyType::TYPE_NAME, $type->getName());
    }
}
