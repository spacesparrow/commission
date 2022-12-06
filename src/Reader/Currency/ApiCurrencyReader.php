<?php

declare(strict_types=1);

namespace App\CommissionTask\Reader\Currency;

use App\CommissionTask\Exception\Reader\CommunicationException;
use App\CommissionTask\Exception\Reader\InvalidDataException;
use App\CommissionTask\Model\Core\Currency;
use App\CommissionTask\Storage\StorageInterface;
use App\CommissionTask\Validator\ValidatorInterface;

class ApiCurrencyReader implements CurrencyReaderInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private StorageInterface $storage,
        private string $apiUrl,
        private int $maxAttempts
    ) {
    }

    public function read(): void
    {
        $this->parse($this->request());
    }

    public function request(): string
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->apiUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        do {
            $currenciesData = curl_exec($curl);
        } while ($currenciesData === false && --$this->maxAttempts);

        curl_close($curl);

        if ($currenciesData === false) {
            throw new CommunicationException();
        }

        return $currenciesData;
    }

    private function parse(string $currenciesData): void
    {
        try {
            $decodedData = json_decode($currenciesData, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new InvalidDataException();
        }

        $this->validator->validate($decodedData);

        foreach ($decodedData['rates'] as $currencyCode => $rate) {
            $currency = new Currency(
                code: $currencyCode,
                rate: (string) $rate,
                base: $currencyCode === $decodedData['base']
            );
            $this->storage->add(StorageInterface::PARTITION_CURRENCIES, $currency->getIdentifier(), $currency);
        }
    }
}
