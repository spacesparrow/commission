<?php

declare(strict_types=1);

namespace App\CommissionTask\Repository;

use App\CommissionTask\Model\Client\ClientInterface;
use App\CommissionTask\Model\Core\ModelInterface;
use App\CommissionTask\Storage\StorageAwareTrait;
use App\CommissionTask\Storage\StorageInterface;

class ClientRepository implements RepositoryInterface
{
    use StorageAwareTrait;
    private const PARTITION_CLIENTS = 'clients';

    public function __construct(StorageInterface $storage)
    {
        $this->setStorage($storage);
    }

    public function get($identifier): ?ClientInterface
    {
        /** @var ClientInterface|null $client */
        $client = $this->getStorage()->get(self::PARTITION_CLIENTS, $identifier);

        return $client;
    }

    public function all(): iterable
    {
        return $this->getStorage()->all(self::PARTITION_CLIENTS);
    }

    public function has($identifier): bool
    {
        return $this->getStorage()->has(self::PARTITION_CLIENTS, $identifier);
    }

    public function add(ModelInterface $element): void
    {
        $this->getStorage()->add(self::PARTITION_CLIENTS, $element->getIdentifier(), $element);
    }

    public function remove($identifier): void
    {
        $this->getStorage()->remove(self::PARTITION_CLIENTS, $identifier);
    }

    public function reset(): void
    {
        $this->getStorage()->reset(self::PARTITION_CLIENTS);
    }
}
