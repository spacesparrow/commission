<?php

declare(strict_types=1);

namespace App\CommissionTask\Validator;

interface ValidatorInterface
{
    public function validate(array $data): void;
}
