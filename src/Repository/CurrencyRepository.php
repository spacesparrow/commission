<?php

declare(strict_types=1);

namespace App\CommissionTask\Repository;

use App\CommissionTask\Model\Core\CurrencyInterface;
use App\CommissionTask\Model\Core\ModelInterface;
use App\CommissionTask\Storage\StorageInterface;

class CurrencyRepository implements RepositoryInterface
{
    private const PARTITION_CURRENCIES = 'currencies';

    public function __construct(protected StorageInterface $storage)
    {
        $this->storage->initPartition(self::PARTITION_CURRENCIES);
    }

    public function get($identifier): ?CurrencyInterface
    {
        /** @var CurrencyInterface|null $currency */
        $currency = $this->storage->get(self::PARTITION_CURRENCIES, $identifier);

        return $currency;
    }

    public function all(): iterable
    {
        return $this->storage->all(self::PARTITION_CURRENCIES);
    }

    public function has($identifier): bool
    {
        return $this->storage->has(self::PARTITION_CURRENCIES, $identifier);
    }

    public function add(ModelInterface $element): void
    {
        $this->storage->add(self::PARTITION_CURRENCIES, $element->getIdentifier(), $element);
    }

    public function remove($identifier): void
    {
        $this->storage->remove(self::PARTITION_CURRENCIES, $identifier);
    }

    public function reset(): void
    {
        $this->storage->reset(self::PARTITION_CURRENCIES);
    }

    public function findUsingClosure(callable $closure): iterable
    {
        return array_filter((array) $this->all(), $closure);
    }
}
