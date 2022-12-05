<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Functional;

use App\CommissionTask\Application;
use App\CommissionTask\Charger\FeeChargerInterface;
use App\CommissionTask\Charger\Withdraw\PrivateClientWithdrawFeeCharger;
use App\CommissionTask\Converter\CurrencyConverter;
use App\CommissionTask\Converter\CurrencyConverterInterface;
use App\CommissionTask\Kernel\Config;
use App\CommissionTask\Kernel\ConfigInterface;
use App\CommissionTask\Kernel\Container;
use App\CommissionTask\Kernel\ContainerInterface;
use App\CommissionTask\Processor\OperationProcessor;
use App\CommissionTask\Processor\ProcessorInterface;
use App\CommissionTask\Reader\Currency\ApiCurrencyReader;
use App\CommissionTask\Reader\Currency\CurrencyReaderInterface;
use App\CommissionTask\Reader\Input\FileInputReader;
use App\CommissionTask\Tests\Functional\Fixtures\ApiCurrencyDataFixture;
use App\CommissionTask\Tests\Functional\Fixtures\ConfigDataFixture;
use App\CommissionTask\Tests\Functional\Fixtures\CsvOperationDataFixture;
use PHPUnit\Framework\TestCase;

final class ApplicationTest extends TestCase
{
    private Application $app;

    /**
     * @throws \JsonException
     */
    protected function setUp(): void
    {
        $container = new Container();
        $container->init();
        $config = $this->mockConfig();
        [$fileInputReader, $currencyReader] = $this->mockReaders($container);
        $currencyConverter = $this->mockCurrencyConverter($container, $currencyReader);
        $privateClientWithdrawFeeCharger = $this->mockFeeCharger($container, $config, $currencyConverter);
        $operationProcessor = $this->mockOperationProcessor($container, $privateClientWithdrawFeeCharger);

        $container->set('app.reader.input.file', $fileInputReader);
        $container->set('app.config', $config);
        $container->set('app.reader.currency', $currencyReader);
        $container->set('app.converter.currency', $currencyConverter);
        $container->set('app.charger.fee.withdraw_private', $privateClientWithdrawFeeCharger);
        $container->set('app.processor.operation', $operationProcessor);

        $this->app = new Application(
            $container->get('app.factory.operation'),
            $fileInputReader,
            $operationProcessor,
            $container->get('app.output.console')
        );
    }

    /**
     * @dataProvider dataProviderForRunTesting
     * @covers       \App\CommissionTask\Application::run
     */
    public function testRun(string $pathToFile, string $expectedOutput): void
    {
        $argv = ['script.php', $pathToFile];
        $this->app->run($argv);

        $this->expectOutputString($expectedOutput);
    }

    public function dataProviderForRunTesting(): array
    {
        $expectedFees = [
            '0.60',
            '3.00',
            '0.00',
            '0.06',
            '1.50',
            '0',
            '0.70',
            '0.30',
            '0.30',
            '3.00',
            '0.00',
            '0.00',
            '8612',
        ];

        return [
            'run application with example data' => [
                'input.example.csv',
                implode(PHP_EOL, $expectedFees) . PHP_EOL,
            ],
        ];
    }

    private function mockConfig(): ConfigInterface
    {
        $config = $this->getMockBuilder(Config::class)
            ->onlyMethods(['getAllConfigValues', 'getEnvVarByName'])
            ->getMock();
        $config
            ->method('getEnvVarByName')
            ->with('CURRENCY_API_URL')
            ->willReturn('https://api.example.com');
        $config
            ->method('getAllConfigValues')
            ->willReturn(ConfigDataFixture::getTestData());

        return $config;
    }

    /**
     * @throws \JsonException
     */
    private function mockReaders(ContainerInterface $container): array
    {
        $fileInputReader = $this->createStub(FileInputReader::class);
        $fileInputReader->method('read')->willReturnCallback(static function () {
            yield from CsvOperationDataFixture::getTestData();
        });

        $currencyReader = $this
            ->getMockBuilder(ApiCurrencyReader::class)
            ->enableOriginalConstructor()
            ->setConstructorArgs([
                $container->get('app.validator.currency_response'),
                $container->get('app.repository.currency'),
                $container->get('app.config')->getEnvVarByName('CURRENCY_API_URL'),
                $container->get('app.config')->getConfigParamByName('parameters.reader.max_attempts')
            ])
            ->onlyMethods(['request'])
            ->getMock();
        $currencyReader->method('request')->willReturn(ApiCurrencyDataFixture::getTestData());

        return [$fileInputReader, $currencyReader];
    }

    private function mockCurrencyConverter(
        ContainerInterface $container,
        CurrencyReaderInterface $currencyReader
    ): CurrencyConverterInterface {
        return $this->getMockBuilder(CurrencyConverter::class)
            ->enableOriginalConstructor()
            ->setConstructorArgs([$container->get('app.repository.currency'), $currencyReader])
            ->onlyMethods([])
            ->getMock();
    }

    private function mockFeeCharger(
        ContainerInterface $container,
        ConfigInterface $config,
        CurrencyConverterInterface $currencyConverter
    ): FeeChargerInterface {
        return $this->getMockBuilder(PrivateClientWithdrawFeeCharger::class)
            ->enableOriginalConstructor()
            ->setConstructorArgs([
                $currencyConverter,
                $container->get('app.repository.operation'),
                $config->getConfigParamByName('parameters.fee.withdraw.private.percent'),
                $config->getConfigParamByName('parameters.fee.withdraw.private.free_count_per_week'),
                $config->getConfigParamByName('parameters.fee.withdraw.private.free_amount_per_week'),
                $config->getConfigParamByName('parameters.currency.base_currency_code')
            ])
            ->onlyMethods([])
            ->getMock();
    }

    private function mockOperationProcessor(
        ContainerInterface $container,
        FeeChargerInterface $mockedCharger
    ): ProcessorInterface {
        return $this->getMockBuilder(OperationProcessor::class)
            ->enableOriginalConstructor()
            ->setConstructorArgs(
                [
                    [
                        $container->get('app.charger.fee.deposit'),
                        $container->get('app.charger.fee.withdraw_business'),
                        $mockedCharger
                    ],
                    $container->get('app.repository.operation')
                ]
            )
            ->onlyMethods([])
            ->getMock();
    }
}
