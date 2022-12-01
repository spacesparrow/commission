<?php

declare(strict_types=1);

namespace App\CommissionTask\Repository;

use App\CommissionTask\Model\Core\ModelInterface;
use App\CommissionTask\Model\Operation\OperationInterface;
use App\CommissionTask\Storage\StorageInterface;

class OperationRepository implements RepositoryInterface
{
    private const PARTITION_OPERATIONS = 'operations';

    public function __construct(protected StorageInterface $storage)
    {
        $this->storage->initPartition(self::PARTITION_OPERATIONS);
    }

    public function get(string $identifier): ?OperationInterface
    {
        /** @var OperationInterface|null $operation */
        $operation = $this->storage->get(self::PARTITION_OPERATIONS, $identifier);

        return $operation;
    }

    public function all(): iterable
    {
        return $this->storage->all(self::PARTITION_OPERATIONS);
    }

    public function add(ModelInterface $element): void
    {
        $this->storage->add(self::PARTITION_OPERATIONS, $element->getIdentifier(), $element);
    }

    public function findUsingClosure(callable $closure): iterable
    {
        return array_filter((array) $this->all(), $closure);
    }
}
