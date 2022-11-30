<?php

declare(strict_types=1);

namespace App\CommissionTask\Validator\Reader;

use App\CommissionTask\Exception\Validator\Reader\MissedSupportedClientTypesException;
use App\CommissionTask\Exception\Validator\Reader\MissedSupportedOperationTypesException;
use App\CommissionTask\Exception\Validator\Reader\UnsupportedClientTypeException;
use App\CommissionTask\Exception\Validator\Reader\UnsupportedOperationTypeException;
use App\CommissionTask\Kernel\ConfigAwareInterface;
use App\CommissionTask\Kernel\ConfigAwareTrait;
use App\CommissionTask\Kernel\ConfigInterface;
use App\CommissionTask\Validator\ValidatorInterface;

class FileInputReaderValidator implements ValidatorInterface, ConfigAwareInterface
{
    use ConfigAwareTrait;

    public function __construct(ConfigInterface $config)
    {
        $this->setConfig($config);
    }

    public function validate($data): void
    {
        $supportedClientTypes = $this->getConfig()->getConfigParamByName('parameters.client.types');

        if (empty($supportedClientTypes)) {
            throw new MissedSupportedClientTypesException();
        }

        if (!in_array($data['client_type'], $supportedClientTypes, true)) {
            $message = sprintf(
                UnsupportedClientTypeException::MESSAGE_PATTERN,
                $data['client_type'],
                implode(',', $supportedClientTypes)
            );
            throw new UnsupportedClientTypeException($message);
        }

        $supportedOperationTypes = $this->getConfig()->getConfigParamByName('parameters.operation.types');

        if (empty($supportedOperationTypes)) {
            throw new MissedSupportedOperationTypesException();
        }

        if (!in_array($data['operation_type'], $supportedOperationTypes, true)) {
            $message = sprintf(
                UnsupportedOperationTypeException::MESSAGE_PATTERN,
                $data['operation_type'],
                implode(',', $supportedOperationTypes)
            );
            throw new UnsupportedOperationTypeException($message);
        }
    }
}
