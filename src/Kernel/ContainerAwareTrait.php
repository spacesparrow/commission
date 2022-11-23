<?php

declare(strict_types=1);

namespace App\CommissionTask\Kernel;

trait ContainerAwareTrait
{
    protected ContainerInterface $container;

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }
}
