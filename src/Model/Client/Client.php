<?php

declare(strict_types=1);

namespace App\CommissionTask\Model\Client;

use App\CommissionTask\Model\Core\ModelInterface;

readonly class Client implements ModelInterface
{
    public const TYPE_PRIVATE = 'private';
    public const TYPE_BUSINESS = 'business';

    public function __construct(private int $id, private string $type)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getIdentifier(): string
    {
        return (string) $this->id;
    }
}
