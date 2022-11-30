<?php

declare(strict_types=1);

namespace App\CommissionTask\Processor;

use App\CommissionTask\Charger\FeeChargerInterface;
use App\CommissionTask\Model\Operation\OperationInterface;
use App\CommissionTask\Repository\RepositoryInterface;

class OperationProcessor implements ProcessorInterface
{
    /** @var iterable|FeeChargerInterface[] */
    private iterable $chargers;

    private RepositoryInterface $operationRepository;

    /**
     * @param iterable|FeeChargerInterface[] $chargers
     */
    public function __construct(iterable $chargers, RepositoryInterface $operationRepository)
    {
        $this->chargers = $chargers;
        $this->operationRepository = $operationRepository;
    }

    public function process(OperationInterface $operation): void
    {
        /** @var FeeChargerInterface $charger */
        foreach ($this->chargers as $charger) {
            if ($charger->supports($operation)) {
                $charger->charge($operation);
            }
        }

        $this->operationRepository->add($operation);
    }
}
