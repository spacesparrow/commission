<?php

declare(strict_types=1);

namespace App\CommissionTask\Processor;

use App\CommissionTask\Model\Operation\Operation;

interface ProcessorInterface
{
    public function process(Operation $operation): \Stringable|string;
}
