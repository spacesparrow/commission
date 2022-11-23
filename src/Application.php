<?php

declare(strict_types=1);

namespace App\CommissionTask;

use App\CommissionTask\Kernel\Container;
use App\CommissionTask\Kernel\ContainerAwareInterface;
use App\CommissionTask\Kernel\ContainerAwareTrait;

final class Application implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function __construct()
    {
        $this->setContainer(new Container());
        $this->getContainer()->init();
    }

    public function run(int $argc, array $argv): void
    {
    }
}
