<?php

declare(strict_types=1);

namespace App\CommissionTask\Reader\Input;

class FileInputReader implements InputReaderInterface
{
    /** @param string $source */
    public function read($source): \Generator
    {
        $file = fopen($source, 'rb');

        while (!feof($file)) {
            $row = fgetcsv($file);

            if (!$row) {
                return;
            }

            $operationData = array_combine(
                ['processed_at', 'client_id', 'client_type', 'operation_type', 'amount', 'currency'],
                $row
            );

            yield $operationData;
        }
    }
}
