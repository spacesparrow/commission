<?php

declare(strict_types=1);

namespace App\CommissionTask\Repository;

use App\CommissionTask\Model\Core\CurrencyInterface;
use App\CommissionTask\Model\Core\ModelInterface;
use App\CommissionTask\Storage\StorageAwareTrait;
use App\CommissionTask\Storage\StorageInterface;

class CurrencyRepository implements RepositoryInterface
{
    use StorageAwareTrait;
    private const PARTITION_CURRENCIES = 'currencies';

    public function __construct(StorageInterface $storage)
    {
        $this->setStorage($storage);
    }

    public function get($identifier): ?CurrencyInterface
    {
        /** @var CurrencyInterface|null $currency */
        $currency = $this->getStorage()->get(self::PARTITION_CURRENCIES, $identifier);

        return $currency;
    }

    public function all(): iterable
    {
        return $this->getStorage()->all(self::PARTITION_CURRENCIES);
    }

    public function has($identifier): bool
    {
        return $this->getStorage()->has(self::PARTITION_CURRENCIES, $identifier);
    }

    public function add($identifier, ModelInterface $element): void
    {
        $this->getStorage()->add(self::PARTITION_CURRENCIES, $identifier, $element);
    }

    public function remove($identifier): void
    {
        $this->getStorage()->remove(self::PARTITION_CURRENCIES, $identifier);
    }

    public function reset(): void
    {
        $this->getStorage()->reset(self::PARTITION_CURRENCIES);
    }
}
