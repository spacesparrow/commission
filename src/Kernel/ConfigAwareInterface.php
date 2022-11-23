<?php

declare(strict_types=1);

namespace App\CommissionTask\Kernel;

interface ConfigAwareInterface
{
    public function getConfig(): ConfigInterface;

    public function setConfig(ConfigInterface $config): void;
}
