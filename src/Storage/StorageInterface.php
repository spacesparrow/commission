<?php

declare(strict_types=1);

namespace App\CommissionTask\Storage;

use App\CommissionTask\Model\Core\ModelInterface;

interface StorageInterface
{
    public function initPartition(string $partition): void;

    public function get(string $partition, string $identifier): ?ModelInterface;

    public function all(?string $partition = null): iterable;

    public function has(string $partition, string $identifier): bool;

    public function add(string $partition, string $identifier, ModelInterface $element): void;
}
