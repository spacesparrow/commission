<?php

declare(strict_types=1);

namespace App\CommissionTask\Kernel;

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Yaml\Yaml;

class Config implements ConfigInterface
{
    private const CONFIG_SEPARATOR = '.';

    protected array $config = [];

    public function load(): void
    {
        $this->loadEnvVars();
        $this->loadConfigFile();
    }

    public function loadEnvVars(): void
    {
        (new Dotenv())->load($this->getEnvVarsFilePath());
    }

    public function loadConfigFile(): void
    {
        $this->config = Yaml::parseFile($this->getConfigFilePath()) ?? [];
    }

    public function getEnvVarsFilePath(): string
    {
        $unresolvedPath = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'.env';

        return realpath($unresolvedPath);
    }

    public function getConfigFilePath(): string
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

    public function getEnvVarByName(string $envVarName)
    {
        return $_ENV[$envVarName];
    }

    public function getConfigParamByName(string $paramName)
    {
        $keys = $this->resolveConfigKeys($paramName);
        $value = $this->getConfigArray();

        while ($keys && $value !== []) {
            $key = array_shift($keys);

            $value = $value[$key] ?? null;
        }

        return $value;
    }

    public function resolveConfigKeys(string $key)
    {
        if (empty($key)) {
            return [];
        }

        return explode(self::CONFIG_SEPARATOR, $key);
    }

    protected function getConfigArray(): array
    {
        return $this->config;
    }
}
