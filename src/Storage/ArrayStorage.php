<?php

declare(strict_types=1);

namespace App\CommissionTask\Storage;

use App\CommissionTask\Model\Core\ModelInterface;

class ArrayStorage implements StorageInterface
{
    private array $storage = [];

    public function initPartition(string $partition): void
    {
        if (!isset($this->storage[$partition])) {
            $this->storage[$partition] = [];
        }
    }

    public function get(string $partition, string $identifier): ?ModelInterface
    {
        return $this->storage[$partition][$identifier] ?? null;
    }

    public function all(?string $partition = null): array
    {
        return ($partition !== null ? $this->storage[$partition] : $this->storage) ?? [];
    }

    public function has(string $partition, string $identifier): bool
    {
        return !empty($this->storage[$partition][$identifier]);
    }

    public function add(string $partition, string $identifier, ModelInterface $element): void
    {
        if (!$this->has($partition, $identifier)) {
            $this->storage[$partition][$identifier] = $element;
        }
    }
}
