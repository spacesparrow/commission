<?php

declare(strict_types=1);

namespace App\CommissionTask\Processor;

use App\CommissionTask\Charger\FeeChargerInterface;
use App\CommissionTask\Model\Operation\Operation;
use App\CommissionTask\Storage\StorageInterface;

class OperationProcessor implements ProcessorInterface
{
    /**
     * @param FeeChargerInterface[] $chargers
     */
    public function __construct(private readonly array $chargers, private readonly StorageInterface $storage)
    {
    }

    public function process(Operation $operation): \Stringable|string
    {
        foreach ($this->chargers as $charger) {
            if ($charger->supports($operation)) {
                $fee = $charger->charge($operation);
                $this->storage->add(StorageInterface::PARTITION_OPERATIONS, $operation->getIdentifier(), $operation);

                return $fee;
            }
        }
    }
}
