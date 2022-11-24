<?php

declare(strict_types=1);

namespace App\CommissionTask\Provider;

use App\CommissionTask\Factory\Core\CurrencyFactoryInterface;
use App\CommissionTask\Model\Core\ModelInterface;
use App\CommissionTask\Repository\RepositoryInterface;

class CurrencyProvider implements ProviderInterface
{
    private RepositoryInterface $currencyRepository;

    private CurrencyFactoryInterface $currencyFactory;

    public function __construct(RepositoryInterface $currencyRepository, CurrencyFactoryInterface $currencyFactory)
    {
        $this->currencyRepository = $currencyRepository;
        $this->currencyFactory = $currencyFactory;
    }

    public function provide($identifier, array $data): ModelInterface
    {
        $currency = $this->currencyRepository->get($identifier)
            ?? $this->currencyFactory->createFromCodeAndRate($data['code'], $data['rate'], $data['base']);
        $this->currencyRepository->add($currency);

        return $currency;
    }
}
