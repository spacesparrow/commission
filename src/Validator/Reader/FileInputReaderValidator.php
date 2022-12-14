<?php

declare(strict_types=1);

namespace App\CommissionTask\Validator\Reader;

use App\CommissionTask\Exception\Validator\Reader\UnsupportedClientTypeException;
use App\CommissionTask\Exception\Validator\Reader\UnsupportedOperationTypeException;
use App\CommissionTask\Validator\ValidatorInterface;

class FileInputReaderValidator implements ValidatorInterface
{
    public function __construct(private array $clientTypes, private array $operationTypes)
    {
    }

    public function validate(array $data): void
    {
        if (!in_array($data['client_type'], $this->clientTypes, true)) {
            $message = sprintf(
                UnsupportedClientTypeException::MESSAGE_PATTERN,
                $data['client_type'],
                implode(',', $this->clientTypes)
            );
            throw new UnsupportedClientTypeException($message);
        }

        if (!in_array($data['operation_type'], $this->operationTypes, true)) {
            $message = sprintf(
                UnsupportedOperationTypeException::MESSAGE_PATTERN,
                $data['operation_type'],
                implode(',', $this->operationTypes)
            );
            throw new UnsupportedOperationTypeException($message);
        }
    }
}
