<?php

declare(strict_types=1);

namespace App\CommissionTask\Reader\Currency;

use App\CommissionTask\Exception\Reader\CommunicationException;
use App\CommissionTask\Exception\Reader\InvalidDataException;
use App\CommissionTask\Model\Core\Currency;
use App\CommissionTask\Repository\RepositoryInterface;
use App\CommissionTask\Validator\ValidatorInterface;

class ApiCurrencyReader implements CurrencyReaderInterface
{
    public function __construct(
        protected ValidatorInterface $validator,
        protected RepositoryInterface $currencyRepository,
        protected string $apiUrl,
        protected int $maxAttempts
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
            $currency = new Currency();
            $currency->setCode($currencyCode);
            $currency->setRate((string) $rate);
            $currency->setBase($currencyCode === $decodedData['base']);
            $this->currencyRepository->add($currency);
        }
    }
}
