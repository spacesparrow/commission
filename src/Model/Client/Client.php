<?php

declare(strict_types=1);

namespace App\CommissionTask\Model\Client;

class Client implements ClientInterface
{
    private int $id;

    private string $type;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getIdentifier(): string
    {
        return (string) $this->id;
    }
}
