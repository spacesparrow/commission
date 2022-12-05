<?php

declare(strict_types=1);

namespace App\CommissionTask\Repository;

use App\CommissionTask\Model\Core\CurrencyInterface;
use App\CommissionTask\Model\Core\ModelInterface;
use App\CommissionTask\Storage\StorageInterface;

class CurrencyRepository implements RepositoryInterface
{
    private const PARTITION_CURRENCIES = 'currencies';

    public function __construct(private StorageInterface $storage)
    {
        $this->storage->initPartition(self::PARTITION_CURRENCIES);
    }

    public function get(string $identifier): ?CurrencyInterface
    {
        /** @var CurrencyInterface|null $currency */
        $currency = $this->storage->get(self::PARTITION_CURRENCIES, $identifier);

        return $currency;
    }

    public function all(): iterable
    {
        return $this->storage->all(self::PARTITION_CURRENCIES);
    }

    public function add(ModelInterface $element): void
    {
        $this->storage->add(self::PARTITION_CURRENCIES, $element->getIdentifier(), $element);
    }

    public function findUsingClosure(callable $closure): iterable
    {
        return array_filter((array) $this->all(), $closure);
    }
}
