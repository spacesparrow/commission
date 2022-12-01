<?php

declare(strict_types=1);

namespace App\CommissionTask\Provider;

use App\CommissionTask\Model\Core\ModelInterface;

interface ProviderInterface
{
    public function provide(string $identifier, array $data): ModelInterface;
}
