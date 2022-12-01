<?php

declare(strict_types=1);

namespace App\CommissionTask\Factory\Client;

use App\CommissionTask\Factory\FactoryInterface;
use App\CommissionTask\Model\Client\ClientInterface;

interface ClientFactoryInterface extends FactoryInterface
{
    public function createNew(): ClientInterface;

    public function createFromIdAndType(string $id, string $type): ClientInterface;
}
