<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Unit\Util;

use App\CommissionTask\Model\Client\Client;
use App\CommissionTask\Model\Client\ClientTypeAwareInterface;
use App\CommissionTask\Model\Core\Currency;
use App\CommissionTask\Model\Operation\Operation;
use App\CommissionTask\Model\Operation\OperationInterface;
use App\CommissionTask\Model\Operation\OperationTypeAwareInterface;
use App\CommissionTask\Util\MoneyUtil;
use Brick\Math\BigDecimal;
use Brick\Money\Context\DefaultContext;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;
use PHPUnit\Framework\TestCase;

class MoneyUtilTest extends TestCase
{
    /**
     * @dataProvider dataProviderForCreateMoneyFromOperationSuccessTesting
     * @covers       \App\CommissionTask\Util\MoneyUtil::createMoneyFromOperation
     *
     * @throws UnknownCurrencyException
     */
    public function testCreateMoneyFromOperationSuccess(OperationInterface $operation): void
    {
        $moneyCreated = MoneyUtil::createMoneyFromOperation($operation);

        static::assertInstanceOf(Money::class, $moneyCreated);
        static::assertEquals(new DefaultContext(), $moneyCreated->getContext());
        static::assertSame((float)$operation->getAmount(), BigDecimal::of($operation->getAmount())->toFloat());
        static::assertSame($operation->getCurrency()->getCode(), $operation->getCurrency()->getCode());
    }

    /**
     * @dataProvider dataProviderForCreateMoneyFromOperationThrowsExceptionTesting
     * @covers \App\CommissionTask\Util\MoneyUtil::createMoneyFromOperation
     *
     *
     * @throws UnknownCurrencyException
     */
    public function testCreateMoneyFromOperationThrowsException(
        OperationInterface $operation,
        UnknownCurrencyException $currencyException
    ): void {
        $this->expectExceptionObject($currencyException);

        MoneyUtil::createMoneyFromOperation($operation);
    }

    /**
     * @throws \Exception
     */
    public function dataProviderForCreateMoneyFromOperationSuccessTesting(): array
    {
        return [
            [$this->createTestOperation('EUR', '200.00')],
            [$this->createTestOperation('USD', '50')],
            [$this->createTestOperation('JPY', '50000')],
            [$this->createTestOperation('GBP', '15.05')]
        ];
    }

    /**
     * @throws \Exception
     */
    public function dataProviderForCreateMoneyFromOperationThrowsExceptionTesting(): array
    {
        return [
            [
                $this->createTestOperation('TEST', '200.00'),
                UnknownCurrencyException::unknownCurrency('TEST')
            ],
            [
                $this->createTestOperation('EURO', '200.00'),
                UnknownCurrencyException::unknownCurrency('EURO')
            ],
            [
                $this->createTestOperation('JPB', '200.00'),
                UnknownCurrencyException::unknownCurrency('JPB')
            ],
            [
                $this->createTestOperation('UST', '200.00'),
                UnknownCurrencyException::unknownCurrency('UST')
            ]
        ];
    }

    /**
     * @throws \Exception
     */
    private function createTestOperation(string $currencyCode, string $amount): OperationInterface
    {
        $currency = new Currency();
        $currency->setCode($currencyCode);
        $currency->setBase(false);
        $currency->setRate('1');

        $client = new Client();
        $client->setId(1);
        $client->setType(ClientTypeAwareInterface::TYPE_PRIVATE);

        $operation = new Operation();
        $operation->setProcessedAt(new \DateTime());
        $operation->setAmount($amount);
        $operation->setType(OperationTypeAwareInterface::TYPE_DEPOSIT);
        $operation->setCurrency($currency);
        $operation->setClient($client);

        return $operation;
    }
}
