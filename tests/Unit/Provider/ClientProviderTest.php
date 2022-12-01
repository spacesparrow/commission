<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Unit\Provider;

use App\CommissionTask\Factory\Client\ClientFactory;
use App\CommissionTask\Model\Client\Client;
use App\CommissionTask\Model\Client\ClientInterface;
use App\CommissionTask\Model\Client\ClientTypeAwareInterface;
use App\CommissionTask\Model\Core\ModelInterface;
use App\CommissionTask\Provider\ClientProvider;
use App\CommissionTask\Repository\ClientRepository;
use PHPUnit\Framework\TestCase;

class ClientProviderTest extends TestCase
{
    /**
     * @dataProvider dataProviderForProvideTesting
     * @covers       \App\CommissionTask\Provider\ClientProvider::provide
     */
    public function testProvide(string $identifier, array $data, bool $found): void
    {
        $clientRepositoryMock = $this->createMock(ClientRepository::class);
        $clientFactoryMock = $this->createMock(ClientFactory::class);

        $client = new Client();
        $client->setId((int)$identifier);
        $client->setType($data['client_type']);

        if ($found) {
            $clientRepositoryMock
                ->expects(static::once())
                ->method('get')
                ->with($identifier)
                ->willReturn($client);
            $clientFactoryMock->expects(static::never())->method('createFromIdAndType')->withAnyParameters();
        } else {
            $clientRepositoryMock
                ->expects(static::once())
                ->method('get')
                ->with($identifier)
                ->willReturn(null);
            $clientFactoryMock
                ->expects(static::once())
                ->method('createFromIdAndType')
                ->with($identifier, $data['client_type'])
                ->willReturn($client);
        }

        $clientRepositoryMock->expects(static::once())->method('add');

        $providedClient = (new ClientProvider($clientRepositoryMock, $clientFactoryMock))->provide($identifier, $data);

        static::assertInstanceOf(ModelInterface::class, $providedClient);
        static::assertInstanceOf(ClientInterface::class, $providedClient);
        static::assertSame((int)$identifier, $providedClient->getId());
        static::assertSame($identifier, $providedClient->getIdentifier());
        static::assertSame($data['client_type'], $providedClient->getType());
    }

    public function dataProviderForProvideTesting(): array
    {
        return [
            ['1', ['client_type' => ClientTypeAwareInterface::TYPE_PRIVATE], true],
            ['2', ['client_type' => ClientTypeAwareInterface::TYPE_BUSINESS], false],
            ['3', ['client_type' => ClientTypeAwareInterface::TYPE_PRIVATE], true],
            ['4', ['client_type' => ClientTypeAwareInterface::TYPE_BUSINESS], false]
        ];
    }
}
