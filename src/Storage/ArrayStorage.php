<?php

declare(strict_types=1);

namespace App\CommissionTask\Storage;

use App\CommissionTask\Model\Core\ModelInterface;

class ArrayStorage implements StorageInterface
{
    protected array $storage = [];

    public function get(string $partition, $identifier): ?ModelInterface
    {
        return $this->storage[$partition][$identifier] ?? null;
    }

    public function all(?string $partition = null): array
    {
        return $partition !== null ? $this->storage[$partition] : $this->storage;
    }

    public function has(string $partition, $identifier): bool
    {
        return !empty($this->storage[$partition][$identifier]);
    }

    public function add(string $partition, $identifier, ModelInterface $element): void
    {
        if (!$this->has($partition, $identifier)) {
            $this->storage[$partition][$identifier] = $element;
        }
    }

    public function remove(string $partition, $identifier): void
    {
        if ($this->has($partition, $identifier)) {
            unset($this->storage[$partition][$identifier]);
        }
    }

    public function reset(?string $partition = null): void
    {
        if ($partition) {
            unset($this->storage[$partition]);
        } else {
            unset($this->storage);
        }
    }
}
