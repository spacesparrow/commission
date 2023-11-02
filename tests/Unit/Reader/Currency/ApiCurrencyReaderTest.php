<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Unit\Reader\Currency;

use App\CommissionTask\Exception\Reader\CommunicationException;
use App\CommissionTask\Exception\Reader\InvalidDataException;
use App\CommissionTask\Kernel\Container;
use App\CommissionTask\Reader\Currency\ApiCurrencyReader;
use App\CommissionTask\Storage\ArrayStorage;
use App\CommissionTask\Storage\StorageInterface;
use App\CommissionTask\Tests\Functional\Fixtures\ApiCurrencyDataFixture;
use App\CommissionTask\Validator\Reader\ApiCurrencyReaderResponseValidator;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class ApiCurrencyReaderTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testReadThrowsCommunicationException(): void
    {
        $validatorMock = $this->createMock(ApiCurrencyReaderResponseValidator::class);
        $storageMock = $this->createMock(ArrayStorage::class);
        $apiUrl = '';
        $maxAttempts = 1;
        $apiCurrencyReader = new ApiCurrencyReader($validatorMock, $storageMock, $apiUrl, $maxAttempts);
        $validatorMock->expects($this->never())->method('validate');
        $storageMock->expects($this->never())->method('add');
        $this->expectException(CommunicationException::class);
        $apiCurrencyReader->read();
    }

    /**
     * @throws Exception
     */
    public function testReadThrowsInvalidDataException(): void
    {
        $validatorMock = $this->createMock(ApiCurrencyReaderResponseValidator::class);
        $storageMock = $this->createMock(ArrayStorage::class);
        $apiUrl = 'api.example.com';
        $maxAttempts = 3;
        $currencyReader = $this
            ->getMockBuilder(ApiCurrencyReader::class)
            ->enableOriginalConstructor()
            ->setConstructorArgs([
                $validatorMock,
                $storageMock,
                $apiUrl,
                $maxAttempts
            ])
            ->onlyMethods(['request'])
            ->getMock();
        $currencyReader->expects($this->once())->method('request')->willReturn('{"}');
        $validatorMock->expects($this->never())->method('validate');
        $storageMock->expects($this->never())->method('add');
        $this->expectException(InvalidDataException::class);
        $currencyReader->read();
    }

    /**
     * @throws Exception
     * @throws \JsonException
     */
    public function testReadMocked(): void
    {
        $validatorMock = $this->createMock(ApiCurrencyReaderResponseValidator::class);
        $storageMock = $this->createMock(ArrayStorage::class);
        $apiUrl = 'api.example.com';
        $maxAttempts = 3;
        $currencyReader = $this
            ->getMockBuilder(ApiCurrencyReader::class)
            ->enableOriginalConstructor()
            ->setConstructorArgs([
                $validatorMock,
                $storageMock,
                $apiUrl,
                $maxAttempts
            ])
            ->onlyMethods(['request'])
            ->getMock();
        $currencyReader->expects($this->once())->method('request')->willReturn(ApiCurrencyDataFixture::getTestData());
        $validatorMock->expects($this->once())->method('validate');
        $storageMock->expects($this->exactly(3))->method('add');
        $currencyReader->read();
    }

    public function testRead(): void
    {
        $container = new Container();
        $container->init();
        /** @var StorageInterface|ArrayStorage $arrayStorage */
        $arrayStorage = $container->get('app.storage.array');
        $currencyReader = new ApiCurrencyReader(
            $container->get('app.validator.currency_response'),
            $container->get('app.storage.array'),
            $container->get('app.config')->getEnvVarByName('CURRENCY_API_URL'),
            $container->get('app.config')->getConfigParamByName('parameters.reader.max_attempts')
        );
        $currencyReader->read();
        $this->assertNotEmpty($arrayStorage->all(StorageInterface::PARTITION_CURRENCIES));
    }
}
