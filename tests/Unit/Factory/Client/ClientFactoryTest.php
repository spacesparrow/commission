<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Unit\Factory\Client;

use App\CommissionTask\Factory\Client\ClientFactory;
use App\CommissionTask\Model\Client\ClientInterface;
use App\CommissionTask\Model\Client\ClientTypeAwareInterface;
use App\CommissionTask\Model\Core\ModelInterface;
use PHPUnit\Framework\TestCase;

class ClientFactoryTest extends TestCase
{
    /**
     * @covers \App\CommissionTask\Factory\Client\ClientFactory::createNew
     */
    public function testCreateNew(): void
    {
        $createdClient = (new ClientFactory())->createNew();

        static::assertInstanceOf(ClientInterface::class, $createdClient);
        static::assertInstanceOf(ModelInterface::class, $createdClient);
        static::assertInstanceOf(ClientTypeAwareInterface::class, $createdClient);
    }

    /**
     * @dataProvider dataProviderForCreateFromIdAndTypeTesting
     * @covers       \App\CommissionTask\Factory\Client\ClientFactory::createFromIdAndType
     */
    public function testCreateFromIdAndType(int $id, string $type): void
    {
        $createdClient = (new ClientFactory())->createFromIdAndType($id, $type);

        static::assertInstanceOf(ClientInterface::class, $createdClient);
        static::assertInstanceOf(ModelInterface::class, $createdClient);
        static::assertInstanceOf(ClientTypeAwareInterface::class, $createdClient);
        static::assertSame($id, $createdClient->getId());
        static::assertSame($type, $createdClient->getType());
        static::assertSame($id, $createdClient->getIdentifier());
    }

    public function dataProviderForCreateFromIdAndTypeTesting(): array
    {
        return [
            [1, ClientTypeAwareInterface::TYPE_BUSINESS],
            [2, ClientTypeAwareInterface::TYPE_PRIVATE],
            [3, ClientTypeAwareInterface::TYPE_PRIVATE],
            [4, ClientTypeAwareInterface::TYPE_BUSINESS]
        ];
    }
}
