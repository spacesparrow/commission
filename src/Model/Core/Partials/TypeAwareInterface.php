<?php

declare(strict_types=1);

namespace App\CommissionTask\Model\Core\Partials;

interface TypeAwareInterface
{
    public function getType(): string;

    public function setType(string $type): void;
}
