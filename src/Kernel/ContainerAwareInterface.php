<?php

declare(strict_types=1);

namespace App\CommissionTask\Kernel;

interface ContainerAwareInterface
{
    public function getContainer(): ContainerInterface;

    public function setContainer(ContainerInterface $container): void;
}
