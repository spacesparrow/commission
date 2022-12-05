<?php

declare(strict_types=1);

namespace App\CommissionTask\Validator\Reader;

use App\CommissionTask\Exception\Validator\Reader\BaseCurrencyMisconfigurationException;
use App\CommissionTask\Exception\Validator\Reader\MissedRequiredResponseFieldException;
use App\CommissionTask\Exception\Validator\Reader\SupportedCurrencyMisconfigurationException;
use App\CommissionTask\Validator\ValidatorInterface;

class ApiCurrencyReaderResponseValidator implements ValidatorInterface
{
    public function __construct(
        private array $responseRequiredField,
        private array $supportedCurrencies,
        private string $baseCurrency
    ) {
    }

    public function validate(array $data): void
    {
        if (empty($this->responseRequiredField)) {
            return;
        }

        foreach ($this->responseRequiredField as $requiredField) {
            if (empty($data[$requiredField])) {
                $message = sprintf(MissedRequiredResponseFieldException::MESSAGE_PATTERN, $requiredField);
                throw new MissedRequiredResponseFieldException($message);
            }
        }

        if ($this->baseCurrency !== $data['base']) {
            $message = sprintf(
                BaseCurrencyMisconfigurationException::MESSAGE_PATTERN,
                $this->baseCurrency,
                $data['base']
            );
            throw new BaseCurrencyMisconfigurationException($message);
        }

        $supportedCurrencies = array_column($this->supportedCurrencies, 'code');
        $currenciesReceived = array_keys($data['rates']);

        foreach ($supportedCurrencies as $supportedCurrency) {
            if (!in_array($supportedCurrency, $currenciesReceived, true)) {
                $message = sprintf(SupportedCurrencyMisconfigurationException::MESSAGE_PATTERN, $supportedCurrency);
                throw new SupportedCurrencyMisconfigurationException($message);
            }
        }
    }
}
