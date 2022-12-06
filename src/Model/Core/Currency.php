<?php

declare(strict_types=1);

namespace App\CommissionTask\Model\Core;

class Currency implements ModelInterface
{
    public function __construct(private string $code, private string $rate, private bool $base)
    {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getRate(): string
    {
        return $this->rate;
    }

    public function isBase(): bool
    {
        return $this->base;
    }

    public function getIdentifier(): string
    {
        return $this->code;
    }
}
