<?php

declare(strict_types=1);

namespace App\CommissionTask\Storage;

use App\CommissionTask\Model\Core\ModelInterface;

interface StorageInterface
{
    public const PARTITION_CLIENTS = 'clients';
    public const PARTITION_CURRENCIES = 'currencies';
    public const PARTITION_OPERATIONS = 'operations';

    public const AVAILABLE_PARTITIONS = [
        self::PARTITION_CLIENTS,
        self::PARTITION_CURRENCIES,
        self::PARTITION_OPERATIONS,
    ];

    public function init(): void;

    public function get(string $partition, string $identifier): ?ModelInterface;

    public function all(?string $partition = null): iterable;

    public function has(string $partition, string $identifier): bool;

    public function add(string $partition, string $identifier, ModelInterface $element): void;
}
