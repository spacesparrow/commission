<?php

declare(strict_types=1);

namespace App\CommissionTask\Repository;

use App\CommissionTask\Model\Core\ModelInterface;
use App\CommissionTask\Storage\StorageAwareInterface;

interface RepositoryInterface extends StorageAwareInterface
{
    public function get($identifier): ?ModelInterface;

    public function all(): iterable;

    public function has($identifier): bool;

    public function add(ModelInterface $element): void;

    public function remove($identifier): void;

    public function reset(): void;
}
