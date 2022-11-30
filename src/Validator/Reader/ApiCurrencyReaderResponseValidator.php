<?php

declare(strict_types=1);

namespace App\CommissionTask\Validator\Reader;

use App\CommissionTask\Exception\Validator\Reader\BaseCurrencyMisconfigurationException;
use App\CommissionTask\Exception\Validator\Reader\MissedRequiredResponseFieldException;
use App\CommissionTask\Exception\Validator\Reader\SupportedCurrencyMisconfigurationException;
use App\CommissionTask\Kernel\ConfigAwareInterface;
use App\CommissionTask\Kernel\ConfigAwareTrait;
use App\CommissionTask\Kernel\ConfigInterface;
use App\CommissionTask\Validator\ValidatorInterface;

class ApiCurrencyReaderResponseValidator implements ValidatorInterface, ConfigAwareInterface
{
    use ConfigAwareTrait;

    public function __construct(ConfigInterface $config)
    {
        $this->setConfig($config);
    }

    public function validate($data): void
    {
        $responseRequiredFields = $this->getConfig()->getConfigParamByName('parameters.reader.response_required_fields');

        if (empty($responseRequiredFields)) {
            return;
        }

        foreach ($responseRequiredFields as $requiredField) {
            if (empty($data[$requiredField])) {
                $message = sprintf(MissedRequiredResponseFieldException::MESSAGE_PATTERN, $requiredField);
                throw new MissedRequiredResponseFieldException($message);
            }
        }

        $baseCurrencyInConfig = $this->getConfig()->getConfigParamByName('parameters.currency.base_currency_code');

        if ($baseCurrencyInConfig !== $data['base']) {
            $message = sprintf(
                BaseCurrencyMisconfigurationException::MESSAGE_PATTERN,
                $baseCurrencyInConfig,
                $data['base']
            );
            throw new BaseCurrencyMisconfigurationException($message);
        }

        $supportedCurrencies = array_column(
            $this->getConfig()->getConfigParamByName('parameters.currency.supported'),
            'code'
        );

        $currenciesReceived = array_keys($data['rates']);

        foreach ($supportedCurrencies as $supportedCurrency) {
            if (!in_array($supportedCurrency, $currenciesReceived, true)) {
                $message = sprintf(SupportedCurrencyMisconfigurationException::MESSAGE_PATTERN, $supportedCurrency);
                throw new SupportedCurrencyMisconfigurationException($message);
            }
        }
    }
}
