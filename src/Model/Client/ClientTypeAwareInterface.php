<?php

declare(strict_types=1);

namespace App\CommissionTask\Model\Client;

use App\CommissionTask\Model\Core\Partials\TypeAwareInterface;

interface ClientTypeAwareInterface extends TypeAwareInterface
{
    public const TYPE_BUSINESS = 'business';
    public const TYPE_PRIVATE = 'private';
}
