<?php

declare(strict_types=1);

namespace App\CommissionTask\Kernel;

interface ConfigInterface
{
    public function load(): void;

    public function getEnvVarByName(string $envVarName): mixed;

    public function getConfigParamByName(string $paramName): mixed;

    public function getAllConfigValues(): mixed;
}
