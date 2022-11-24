<?php

declare(strict_types=1);

namespace App\CommissionTask\Provider;

use App\CommissionTask\Factory\Client\ClientFactoryInterface;
use App\CommissionTask\Model\Core\ModelInterface;
use App\CommissionTask\Repository\RepositoryInterface;

class ClientProvider implements ProviderInterface
{
    private RepositoryInterface $clientRepository;

    private ClientFactoryInterface $clientFactory;

    public function __construct(RepositoryInterface $clientRepository, ClientFactoryInterface $clientFactory)
    {
        $this->clientRepository = $clientRepository;
        $this->clientFactory = $clientFactory;
    }

    public function provide($identifier, array $data): ModelInterface
    {
        $client = $this->clientRepository->get($identifier)
            ?? $this->clientFactory->createFromIdAndType($identifier, $data['client_type']);
        $this->clientRepository->add($client);

        return $client;
    }
}
