<?php

declare(strict_types=1);

namespace App\CommissionTask\Kernel;

interface ContainerInterface
{
    public function init(): void;

    public function get(string $key): object;

    public function set(string $key, object $instance): void;

    public function has(string $key): bool;
}
