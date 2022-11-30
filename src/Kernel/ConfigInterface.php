<?php

declare(strict_types=1);

namespace App\CommissionTask\Kernel;

interface ConfigInterface
{
    public function load(): void;

    public function loadEnvVars(): void;

    public function loadConfigFile(): void;

    public function getEnvVarsFilePath(): string;

    public function getConfigFilePath(): string;

    public function getEnvVarByName(string $envVarName);

    public function getConfigParamByName(string $paramName);

    public function resolveConfigKeys(string $key);
}
