<?php

declare(strict_types=1);

namespace App\CommissionTask\Model\Client;

use App\CommissionTask\Model\Core\ModelInterface;

interface ClientInterface extends ClientTypeAwareInterface, ModelInterface
{
    public function getId(): int;

    public function setId(int $id): void;
}
