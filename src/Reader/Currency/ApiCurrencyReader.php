<?php

declare(strict_types=1);

namespace App\CommissionTask\Reader\Currency;

use App\CommissionTask\Exception\Reader\CommunicationException;
use App\CommissionTask\Exception\Reader\InvalidDataException;
use App\CommissionTask\Factory\Core\CurrencyFactoryInterface;

class ApiCurrencyReader implements CurrencyReaderInterface
{
    private CurrencyFactoryInterface $currencyFactory;

    public function __construct(CurrencyFactoryInterface $currencyFactory)
    {
        $this->currencyFactory = $currencyFactory;
    }

    public function getCurrencies(): iterable
    {
        return $this->parse($this->request());
    }

    private function request(): string
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, 'https://developers.paysera.com/tasks/api/currency-exchange-rates');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $remainingAttempts = 5;

        do {
            $currenciesData = curl_exec($curl);
        } while ($currenciesData === false && --$remainingAttempts);

        curl_close($curl);

        if ($currenciesData === false) {
            throw new CommunicationException();
        }

        return $currenciesData;
    }

    private function parse(string $currenciesData): array
    {
        try {
            $decodedData = json_decode($currenciesData, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InvalidDataException();
        }

        $currencies = [];

        foreach ($decodedData['rates'] as $currencyCode => $rate) {
            $currencies[] = $this->currencyFactory->createFromCodeAndRate(
                $currencyCode,
                (string) $rate,
                $currencyCode === $decodedData['base']
            );
        }

        return $currencies;
    }
}
