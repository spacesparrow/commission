<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Unit\Kernel;

use App\CommissionTask\Exception\Kernel\UndefinedInstanceException;
use App\CommissionTask\Factory\Operation\OperationFactory;
use App\CommissionTask\Kernel\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testAllMethods(): void
    {
        $container = new Container();
        $container->init();
        $this->assertInstanceOf(OperationFactory::class, $container->get('app.factory.operation'));
        $container->set('app.factory.operation', new \stdClass());
        $this->assertInstanceOf(\stdClass::class, $container->get('app.factory.operation'));
        $this->expectException(UndefinedInstanceException::class);
        $container->get('random.stuff');
    }
}
