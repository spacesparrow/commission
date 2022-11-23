<?php

declare(strict_types=1);

namespace App\CommissionTask\Reader\Currency;

use App\CommissionTask\Exception\Reader\CommunicationException;
use App\CommissionTask\Exception\Reader\InvalidDataException;
use App\CommissionTask\Factory\Core\CurrencyFactoryInterface;
use App\CommissionTask\Kernel\ConfigAwareInterface;
use App\CommissionTask\Kernel\ConfigAwareTrait;
use App\CommissionTask\Kernel\ConfigInterface;
use App\CommissionTask\Validator\ValidatorInterface;

class ApiCurrencyReader implements CurrencyReaderInterface, ConfigAwareInterface
{
    use ConfigAwareTrait;

    private CurrencyFactoryInterface $currencyFactory;

    private ValidatorInterface $validator;

    public function __construct(
        CurrencyFactoryInterface $currencyFactory,
        ValidatorInterface $validator,
        ConfigInterface $config
    ) {
        $this->currencyFactory = $currencyFactory;
        $this->validator = $validator;
        $this->setConfig($config);
    }

    public function getCurrencies(): iterable
    {
        return $this->parse($this->request());
    }

    private function request(): string
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->getConfig()->getEnvVarByName('CURRENCY_API_URL'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $remainingAttempts = $this->getConfig()->getConfigParamByName('parameters.reader.max_attempts');

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

        $this->validator->validate($decodedData);
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
