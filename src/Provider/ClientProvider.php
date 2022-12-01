<?php

declare(strict_types=1);

namespace App\CommissionTask\Provider;

use App\CommissionTask\Factory\Client\ClientFactoryInterface;
use App\CommissionTask\Model\Core\ModelInterface;
use App\CommissionTask\Repository\RepositoryInterface;

class ClientProvider implements ProviderInterface
{
    public function __construct(
        protected RepositoryInterface $clientRepository,
        protected ClientFactoryInterface $clientFactory
    ) {
    }

    public function provide(string $identifier, array $data): ModelInterface
    {
        $client = $this->clientRepository->get($identifier)
            ?? $this->clientFactory->createFromIdAndType($identifier, $data['client_type']);
        $this->clientRepository->add($client);

        return $client;
    }
}
