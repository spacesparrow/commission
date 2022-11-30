<?php

declare(strict_types=1);

namespace App\CommissionTask\Factory\Client;

use App\CommissionTask\Model\Client\Client;
use App\CommissionTask\Model\Client\ClientInterface;

class ClientFactory implements ClientFactoryInterface
{
    public function createNew(): ClientInterface
    {
        return new Client();
    }

    public function createFromIdAndType(int $id, string $type): ClientInterface
    {
        $client = $this->createNew();
        $client->setId($id);
        $client->setType($type);

        return $client;
    }
}
