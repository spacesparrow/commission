<?php

declare(strict_types=1);

namespace App\CommissionTask\Kernel;

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Yaml\Yaml;

class Config
{
    private const CONFIG_SEPARATOR = '.';

    private array $config = [];

    public function load(): void
    {
        $this->loadEnvVars();
        $this->loadConfigFile();
    }

    public function getEnvVarByName(string $envVarName): mixed
    {
        return $_ENV[$envVarName];
    }

    public function getConfigParamByName(string $paramName): mixed
    {
        $keys = $this->resolveConfigKeys($paramName);
        $value = $this->getAllConfigValues();

        while ($keys && $value !== []) {
            $key = array_shift($keys);

            $value = $value[$key] ?? null;
        }

        return $value;
    }

    public function getAllConfigValues(): array
    {
        return $this->config;
    }

    private function resolveConfigKeys(string $key): array
    {
        if (empty($key)) {
            return [];
        }

        return explode(self::CONFIG_SEPARATOR, $key);
    }

    private function loadEnvVars(): void
    {
        (new Dotenv())->load($this->getEnvVarsFilePath());
    }

    private function loadConfigFile(): void
    {
        $this->config = Yaml::parseFile($this->getConfigFilePath()) ?? [];
    }

    private function getEnvVarsFilePath(): string
    {
        $unresolvedPath = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'.env';

        return realpath($unresolvedPath);
    }

    private function getConfigFilePath(): string
    {
        $unresolvedPath = __DIR__
            .DIRECTORY_SEPARATOR
            .'..'
            .DIRECTORY_SEPARATOR
            .'..'
            .DIRECTORY_SEPARATOR
            .'config'
            .DIRECTORY_SEPARATOR
            .'config.yaml';

        return realpath($unresolvedPath);
    }
}
