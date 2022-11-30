<?php

declare(strict_types=1);

namespace App\CommissionTask\Model\Client;

interface ClientAwareInterface
{
    public function getClient(): ClientInterface;

    public function setClient(ClientInterface $client): void;
}
