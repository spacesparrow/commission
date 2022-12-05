<?php

declare(strict_types=1);

namespace App\CommissionTask\Processor;

use App\CommissionTask\Charger\FeeChargerInterface;
use App\CommissionTask\Model\Operation\OperationInterface;
use App\CommissionTask\Repository\RepositoryInterface;

class OperationProcessor implements ProcessorInterface
{
    /**
     * @param FeeChargerInterface[] $chargers
     */
    public function __construct(private array $chargers, private RepositoryInterface $operationRepository)
    {
    }

    public function process(OperationInterface $operation): void
    {
        foreach ($this->chargers as $charger) {
            if ($charger->supports($operation)) {
                $charger->charge($operation);
            }
        }

        $this->operationRepository->add($operation);
    }
}
