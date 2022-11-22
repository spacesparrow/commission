<?php

declare(strict_types=1);

namespace App\CommissionTask\Model\Client;

interface ClientInterface extends ClientTypeAwareInterface
{
    public function getId(): int;

    public function setId(int $id): void;
}
