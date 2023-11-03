<?php

declare(strict_types=1);

namespace App\CommissionTask\Model\Client;

enum ClientType: string
{
    case PRIVATE = 'private';
    case BUSINESS = 'business';
}
