<?php

declare(strict_types=1);

namespace App\CommissionTask\Kernel;

use App\CommissionTask\Charger\Deposit\DepositFeeCharger;
use App\CommissionTask\Charger\Withdraw\BusinessClientWithdrawFeeCharger;
use App\CommissionTask\Charger\Withdraw\PrivateClientWithdrawFeeCharger;
use App\CommissionTask\Converter\CurrencyConverter;
use App\CommissionTask\Exception\Kernel\UndefinedInstanceException;
use App\CommissionTask\Factory\Operation\OperationFactory;
use App\CommissionTask\Output\ConsoleOutput;
use App\CommissionTask\Processor\OperationProcessor;
use App\CommissionTask\Reader\Currency\ApiCurrencyReader;
use App\CommissionTask\Reader\Input\FileInputReader;
use App\CommissionTask\Storage\ArrayStorage;
use App\CommissionTask\Validator\Reader\ApiCurrencyReaderResponseValidator;
use App\CommissionTask\Validator\Reader\FileInputReaderValidator;

class Container implements ContainerInterface
{
    private array $instances = [];

    public function init(): void
    {
        // Initialize application config
        $config = new Config();
        $config->load();

        // Register config
        $this->set('app.config', $config);

        $storage = new ArrayStorage();
        $storage->init();

        // Register storage
        $this->set('app.storage.array', $storage);

        $this->registerFactories();
        $this->registerValidators();
        $this->registerReaders();

        // Register currency converter
        $this->set(
            'app.converter.currency',
            new CurrencyConverter($this->get('app.storage.array'), $this->get('app.reader.currency'))
        );

        $this->registerFeeChargers();

        // Register operation processor
        $this->set(
            'app.processor.operation',
            new OperationProcessor(
                [
                    $this->get('app.charger.fee.deposit'),
                    $this->get('app.charger.fee.withdraw_business'),
                    $this->get('app.charger.fee.withdraw_private'),
                ],
                $this->get('app.storage.array')
            )
        );

        $this->set('app.output.console', new ConsoleOutput());
    }

    public function get(string $key): object
    {
        if (empty($this->instances[$key])) {
            throw new UndefinedInstanceException();
        }

        return $this->instances[$key];
    }

    public function set(string $key, object $instance): void
    {
        $this->instances[$key] = $instance;
    }

    private function registerFactories(): void
    {
        $this->set('app.factory.operation', new OperationFactory($this->get('app.storage.array')));
    }

    private function registerValidators(): void
    {
        $this->set(
            'app.validator.currency_response',
            new ApiCurrencyReaderResponseValidator(
                $this->get('app.config')->getConfigParamByName('parameters.currency.supported'),
                $this->get('app.config')->getConfigParamByName('parameters.currency.base_currency_code')
            )
        );
        $this->set(
            'app.validator.file_input',
            new FileInputReaderValidator(
                $this->get('app.config')->getConfigParamByName('parameters.client.types'),
                $this->get('app.config')->getConfigParamByName('parameters.operation.types')
            )
        );
    }

    private function registerReaders(): void
    {
        $this->set(
            'app.reader.currency',
            new ApiCurrencyReader(
                $this->get('app.validator.currency_response'),
                $this->get('app.storage.array'),
                $this->get('app.config')->getEnvVarByName('CURRENCY_API_URL'),
                $this->get('app.config')->getConfigParamByName('parameters.reader.max_attempts')
            )
        );
        $this->set('app.reader.input.file', new FileInputReader($this->get('app.validator.file_input')));
    }

    private function registerFeeChargers(): void
    {
        $this->set(
            'app.charger.fee.deposit',
            new DepositFeeCharger(
                $this->get('app.config')->getConfigParamByName('parameters.fee.deposit.percent')
            )
        );
        $this->set(
            'app.charger.fee.withdraw_business',
            new BusinessClientWithdrawFeeCharger(
                $this->get('app.config')->getConfigParamByName('parameters.fee.withdraw.business.percent')
            )
        );
        $this->set(
            'app.charger.fee.withdraw_private',
            new PrivateClientWithdrawFeeCharger(
                $this->get('app.converter.currency'),
                $this->get('app.storage.array'),
                $this->get('app.config')->getConfigParamByName('parameters.fee.withdraw.private.percent'),
                $this->get('app.config')->getConfigParamByName('parameters.fee.withdraw.private.free_count_per_week'),
                $this->get('app.config')->getConfigParamByName('parameters.fee.withdraw.private.free_amount_per_week'),
                $this->get('app.config')->getConfigParamByName('parameters.currency.base_currency_code')
            )
        );
    }
}
