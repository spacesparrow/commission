<?php

declare(strict_types=1);

namespace App\CommissionTask\Validator\Reader;

use App\CommissionTask\Exception\Validator\Reader\MissedRequiredResponseFieldException;
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
                throw new MissedRequiredResponseFieldException($requiredField);
            }
        }
    }
}
