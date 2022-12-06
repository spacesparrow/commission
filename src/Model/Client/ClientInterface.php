<?php

declare(strict_types=1);

namespace App\CommissionTask\Model\Client;

use App\CommissionTask\Model\Core\ModelInterface;

interface ClientInterface extends ModelInterface
{
    public const TYPE_BUSINESS = 'business';
    public const TYPE_PRIVATE = 'private';

    public function getId(): int;

    public function getType(): string;
}
