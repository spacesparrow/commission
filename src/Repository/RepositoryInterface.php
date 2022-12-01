<?php

declare(strict_types=1);

namespace App\CommissionTask\Repository;

use App\CommissionTask\Model\Core\ModelInterface;

interface RepositoryInterface
{
    public function get(string $identifier): ?ModelInterface;

    public function all(): iterable;

    public function add(ModelInterface $element): void;

    public function findUsingClosure(callable $closure): iterable;
}
