<?php

declare(strict_types=1);

namespace App\CommissionTask\Kernel;

trait ConfigAwareTrait
{
    protected ConfigInterface $config;

    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    public function setConfig(ConfigInterface $config): void
    {
        $this->config = $config;
    }
}
