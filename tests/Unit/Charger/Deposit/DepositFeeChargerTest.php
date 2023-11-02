<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Unit\Charger\Deposit;

use App\CommissionTask\Charger\Deposit\DepositFeeCharger;
use App\CommissionTask\Model\Client\Client;
use App\CommissionTask\Model\Operation\Operation;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Money\Exception\UnknownCurrencyException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DepositFeeChargerTest extends TestCase
{
    private readonly DepositFeeCharger $charger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->charger = new DepositFeeCharger(0.1);
    }

    public static function dataProviderForChargeTesting(): array
    {
        $client = new Client(1, Client::TYPE_BUSINESS);
        $firstOperation = new Operation('EUR', new \DateTime(), '5', Operation::TYPE_DEPOSIT, $client);
        $secondOperation = new Operation('EUR', new \DateTime(), '15', Operation::TYPE_DEPOSIT, $client);
        $thirdOperation = new Operation('EUR', new \DateTime(), '0.5', Operation::TYPE_DEPOSIT, $client);
        $fourthOperation = new Operation('EUR', new \DateTime(), '0', Operation::TYPE_DEPOSIT, $client);

        return [
            [$firstOperation, '0.50'],
            [$secondOperation, '1.50'],
            [$thirdOperation, '0.05'],
            [$fourthOperation, '0.00'],
        ];
    }

    /**
     * @throws RoundingNecessaryException
     * @throws MathException
     * @throws UnknownCurrencyException
     * @throws NumberFormatException
     */
    #[DataProvider('dataProviderForChargeTesting')]
    public function testCharge(Operation $operation, string $expectedCharge): void
    {
        $this->assertSame($expectedCharge, (string) $this->charger->charge($operation));
    }

    public static function dataProviderForSupportsTesting(): array
    {
        $client = new Client(1, Client::TYPE_BUSINESS);
        $firstOperation = new Operation('EUR', new \DateTime(), '5', Operation::TYPE_DEPOSIT, $client);
        $secondOperation = new Operation('EUR', new \DateTime(), '15', Operation::TYPE_WITHDRAW, $client);
        $thirdOperation = new Operation('EUR', new \DateTime(), '0.5', Operation::TYPE_WITHDRAW, $client);
        $fourthOperation = new Operation('EUR', new \DateTime(), '0', Operation::TYPE_DEPOSIT, $client);

        return [
            [$firstOperation, true],
            [$secondOperation, false],
            [$thirdOperation, false],
            [$fourthOperation, true],
        ];
    }

    #[DataProvider('dataProviderForSupportsTesting')]
    public function testSupports(Operation $operation, bool $expectedResult): void
    {
        $this->assertSame($expectedResult, $this->charger->supports($operation));
    }
}
