<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Unit\Processor;

use App\CommissionTask\Charger\Withdraw\PrivateClientWithdrawFeeCharger;
use App\CommissionTask\Model\Operation\Operation;
use App\CommissionTask\Processor\OperationProcessor;
use App\CommissionTask\Storage\ArrayStorage;
use App\CommissionTask\Storage\StorageInterface;
use App\CommissionTask\Tests\Unit\Charger\Withdraw\PrivateClientWithdrawFeeChargerDataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class OperationProcessorTest extends TestCase
{
    /**
     * @throws Exception
     */
    #[DataProviderExternal(PrivateClientWithdrawFeeChargerDataProvider::class, 'dataProviderForSupportsTesting')]
    public function testProcess(Operation $operation, bool $willBeProcessed): void
    {
        $charger = $this->getMockBuilder(PrivateClientWithdrawFeeCharger::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['charge'])
            ->getMock();
        $storageMock = $this->createMock(ArrayStorage::class);
        if ($willBeProcessed) {
            $charger->expects($this->once())->method('charge')->with($operation)->willReturn('3');
            $storageMock->expects($this->once())
                ->method('add')
                ->with(StorageInterface::PARTITION_OPERATIONS, $operation->getIdentifier(), $operation);
        } else {
            $charger->expects($this->never())->method('charge');
            $storageMock->expects($this->never())->method('add')->withAnyParameters();
        }
        $processor = new OperationProcessor([$charger], $storageMock);
        $processor->process($operation);
    }
}
