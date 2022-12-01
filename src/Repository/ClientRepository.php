<?php

declare(strict_types=1);

namespace App\CommissionTask\Repository;

use App\CommissionTask\Model\Client\ClientInterface;
use App\CommissionTask\Model\Core\ModelInterface;
use App\CommissionTask\Storage\StorageInterface;

class ClientRepository implements RepositoryInterface
{
    private const PARTITION_CLIENTS = 'clients';

    public function __construct(protected StorageInterface $storage)
    {
        $this->storage->initPartition(self::PARTITION_CLIENTS);
    }

    public function get(string $identifier): ?ClientInterface
    {
        /** @var ClientInterface|null $client */
        $client = $this->storage->get(self::PARTITION_CLIENTS, $identifier);

        return $client;
    }

    public function all(): iterable
    {
        return $this->storage->all(self::PARTITION_CLIENTS);
    }

    public function has(string $identifier): bool
    {
        return $this->storage->has(self::PARTITION_CLIENTS, $identifier);
    }

    public function add(ModelInterface $element): void
    {
        $this->storage->add(self::PARTITION_CLIENTS, $element->getIdentifier(), $element);
    }

    public function remove(string $identifier): void
    {
        $this->storage->remove(self::PARTITION_CLIENTS, $identifier);
    }

    public function reset(): void
    {
        $this->storage->reset(self::PARTITION_CLIENTS);
    }

    public function findUsingClosure(callable $closure): iterable
    {
        return array_filter((array) $this->all(), $closure);
    }
}
