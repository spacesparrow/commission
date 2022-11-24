<?php

declare(strict_types=1);

namespace App\CommissionTask\Storage;

interface StorageAwareInterface
{
    public function getStorage(): StorageInterface;

    public function setStorage(StorageInterface $storage): void;
}
