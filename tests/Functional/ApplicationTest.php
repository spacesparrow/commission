<?php

declare(strict_types=1);

namespace App\CommissionTask\Tests\Functional;

use App\CommissionTask\Application;
use App\CommissionTask\Kernel\Config;
use App\CommissionTask\Kernel\Container;
use App\CommissionTask\Reader\Currency\ApiCurrencyReader;
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
        $config = $this->getMockBuilder(Config::class)
            ->onlyMethods(['getConfigArray', 'getEnvVarByName'])
            ->getMock();
        $config
            ->method('getEnvVarByName')
            ->with('CURRENCY_API_URL')
            ->willReturn('https://api.example.com');
        $config
            ->method('getConfigArray')
            ->willReturn(ConfigDataFixture::getTestData());

        $fileInputReader = $this->createStub(FileInputReader::class);
        $fileInputReader->method('read')->willReturnCallback(static function () {
            yield from CsvOperationDataFixture::getTestData();
        });

        $currencyReader = $this
            ->getMockBuilder(ApiCurrencyReader::class)
            ->enableOriginalConstructor()
            ->setConstructorArgs([
                $container->get('app.factory.currency'),
                $container->get('app.validator.currency_response'),
                $container->get('app.repository.currency'),
                $config
            ])
            ->onlyMethods(['request'])
            ->getMock();
        $currencyReader->method('request')->willReturn(ApiCurrencyDataFixture::getTestData());

        $container->replace('app.reader.input.file', $fileInputReader);
        $container->replace('app.config', $config);
        $container->replace('app.reader.currency', $currencyReader);

        $this->app = new Application($container);
    }

    /**
     * @dataProvider dataProviderForRunTesting
     * @covers \App\CommissionTask\Application::run
     */
    public function testRun(string $pathToFile, string $expectedOutput): void
    {
        $argv = ['script.php', $pathToFile];
        $this->app->run(count($argv), $argv);

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
                implode(PHP_EOL, $expectedFees).PHP_EOL,
            ],
        ];
    }
}
