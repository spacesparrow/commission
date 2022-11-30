<?php

declare(strict_types=1);

namespace App\CommissionTask\Model\Core;

interface CurrencyInterface extends ModelInterface
{
    public function getCode(): string;

    public function setCode(string $code): void;

    public function getRate(): string;

    public function setRate(string $rate): void;

    public function isBase(): bool;

    public function setBase(bool $base): void;
}
