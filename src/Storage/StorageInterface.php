<?php

declare(strict_types=1);

namespace App\CommissionTask\Storage;

use App\CommissionTask\Model\Core\ModelInterface;

interface StorageInterface
{
    public function get(string $partition, $identifier): ?ModelInterface;

    public function all(?string $partition = null): iterable;

    public function has(string $partition, $identifier): bool;

    public function add(string $partition, $identifier, ModelInterface $element): void;

    public function remove(string $partition, $identifier): void;

    public function reset(?string $partition = null): void;
}
