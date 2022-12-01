<?php

declare(strict_types=1);

namespace App\CommissionTask\Kernel;

use App\CommissionTask\Charger\Deposit\DepositFeeCharger;
use App\CommissionTask\Charger\Withdraw\BusinessClientWithdrawFeeCharger;
use App\CommissionTask\Charger\Withdraw\PrivateClientWithdrawFeeCharger;
use App\CommissionTask\Converter\CurrencyConverter;
use App\CommissionTask\Exception\Kernel\UndefinedInstanceException;
use App\CommissionTask\Factory\Operation\OperationFactory;
use App\CommissionTask\Processor\OperationProcessor;
use App\CommissionTask\Reader\Currency\ApiCurrencyReader;
use App\CommissionTask\Reader\Input\FileInputReader;
use App\CommissionTask\Repository\ClientRepository;
use App\CommissionTask\Repository\CurrencyRepository;
use App\CommissionTask\Repository\OperationRepository;
use App\CommissionTask\Storage\ArrayStorage;
use App\CommissionTask\Validator\Reader\ApiCurrencyReaderResponseValidator;
use App\CommissionTask\Validator\Reader\FileInputReaderValidator;

class Container implements ContainerInterface
{
    protected array $instances = [];

    public function init(): void
    {
        // Initialize application config
        $config = new Config();
        $config->load();

        // Register config
        $this->set('app.config', $config);

        // Register storage
        $this->set('app.storage.array', new ArrayStorage());

        $this->registerRepositories();
        $this->registerFactories();
        $this->registerValidators();
        $this->registerReaders();

        // Register currency converter
        $this->set('app.converter.currency', new CurrencyConverter($this->get('app.repository.currency')));

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
                $this->get('app.repository.operation')
            )
        );
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

    private function registerRepositories(): void
    {
        $this->set('app.repository.currency', new CurrencyRepository($this->get('app.storage.array')));
        $this->set('app.repository.client', new ClientRepository($this->get('app.storage.array')));
        $this->set('app.repository.operation', new OperationRepository($this->get('app.storage.array')));
    }

    private function registerFactories(): void
    {
        $this->set(
            'app.factory.operation',
            new OperationFactory(
                $this->get('app.repository.client'),
                $this->get('app.repository.currency')
            )
        );
    }

    private function registerValidators(): void
    {
        $this->set(
            'app.validator.currency_response',
            new ApiCurrencyReaderResponseValidator(
                $this->get('app.config')->getConfigParamByName('parameters.reader.response_required_fields'),
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
                $this->get('app.repository.currency'),
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
                $this->get('app.converter.currency'),
                $this->get('app.config')->getConfigParamByName('parameters.fee.deposit.percent')
            )
        );
        $this->set(
            'app.charger.fee.withdraw_business',
            new BusinessClientWithdrawFeeCharger(
                $this->get('app.converter.currency'),
                $this->get('app.config')->getConfigParamByName('parameters.fee.withdraw.business.percent')
            )
        );
        $this->set(
            'app.charger.fee.withdraw_private',
            new PrivateClientWithdrawFeeCharger(
                $this->get('app.converter.currency'),
                $this->get('app.repository.operation'),
                $this->get('app.config')->getConfigParamByName('parameters.fee.withdraw.private.percent'),
                $this->get('app.config')->getConfigParamByName('parameters.fee.withdraw.private.free_count_per_week'),
                $this->get('app.config')->getConfigParamByName('parameters.fee.withdraw.private.free_amount_per_week'),
                $this->get('app.config')->getConfigParamByName('parameters.currency.base_currency_code')
            )
        );
    }
}
