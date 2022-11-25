<?php

declare(strict_types=1);

namespace App\CommissionTask\Repository;

use App\CommissionTask\Model\Core\ModelInterface;
use App\CommissionTask\Model\Operation\OperationInterface;
use App\CommissionTask\Storage\StorageAwareTrait;
use App\CommissionTask\Storage\StorageInterface;

class OperationRepository implements RepositoryInterface
{
    use StorageAwareTrait;
    private const PARTITION_OPERATIONS = 'operations';

    public function __construct(StorageInterface $storage)
    {
        $this->setStorage($storage);
    }

    public function get($identifier): ?OperationInterface
    {
        /** @var OperationInterface|null $operation */
        $operation = $this->getStorage()->get(self::PARTITION_OPERATIONS, $identifier);

        return $operation;
    }

    public function all(): iterable
    {
        return $this->getStorage()->all(self::PARTITION_OPERATIONS);
    }

    public function has($identifier): bool
    {
        return $this->getStorage()->has(self::PARTITION_OPERATIONS, $identifier);
    }

    public function add(ModelInterface $element): void
    {
        $this->getStorage()->add(self::PARTITION_OPERATIONS, $element->getIdentifier(), $element);
    }

    public function remove($identifier): void
    {
        $this->getStorage()->remove(self::PARTITION_OPERATIONS, $identifier);
    }

    public function reset(): void
    {
        $this->getStorage()->reset(self::PARTITION_OPERATIONS);
    }
}
