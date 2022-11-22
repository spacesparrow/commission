<?php

declare(strict_types=1);

namespace App\CommissionTask\Model\Core;

class Currency implements CurrencyInterface
{
    private string $code;

    private string $rate;

    private bool $base;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getRate(): string
    {
        return $this->rate;
    }

    public function setRate(string $rate): void
    {
        $this->rate = $rate;
    }

    public function isBase(): bool
    {
        return $this->base;
    }

    public function setBase(bool $base): void
    {
        $this->base = $base;
    }
}
