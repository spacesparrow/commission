<?php

declare(strict_types=1);

namespace App\CommissionTask\Storage;

trait StorageAwareTrait
{
    protected StorageInterface $storage;

    public function getStorage(): StorageInterface
    {
        return $this->storage;
    }

    public function setStorage(StorageInterface $storage): void
    {
        $this->storage = $storage;
    }
}
