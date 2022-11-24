<?php

declare(strict_types=1);

namespace App\CommissionTask\Kernel;

use App\CommissionTask\Converter\CurrencyConverter;
use App\CommissionTask\Exception\Kernel\UndefinedInstanceException;
use App\CommissionTask\Factory\Client\ClientFactory;
use App\CommissionTask\Factory\Core\CurrencyFactory;
use App\CommissionTask\Factory\Operation\OperationFactory;
use App\CommissionTask\Provider\ClientProvider;
use App\CommissionTask\Provider\CurrencyProvider;
use App\CommissionTask\Reader\Currency\ApiCurrencyReader;
use App\CommissionTask\Reader\Input\FileInputReader;
use App\CommissionTask\Repository\ClientRepository;
use App\CommissionTask\Repository\CurrencyRepository;
use App\CommissionTask\Storage\ArrayStorage;
use App\CommissionTask\Validator\Reader\ApiCurrencyReaderResponseValidator;

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

        // Register repositories
        $this->set('app.repository.currency', new CurrencyRepository($this->get('app.storage.array')));
        $this->set('app.repository.client', new ClientRepository($this->get('app.storage.array')));

        // Register factories
        $this->set('app.factory.currency', new CurrencyFactory());
        $this->set('app.factory.client', new ClientFactory());

        // Register providers
        $this->set('app.provider.currency',
            new CurrencyProvider($this->get('app.repository.currency'), $this->get('app.factory.currency')));
        $this->set('app.provider.client',
            new ClientProvider($this->get('app.repository.client'), $this->get('app.factory.client')));

        // Register operation factory
        $this->set(
            'app.factory.operation',
            new OperationFactory($this->get('app.provider.currency'), $this->get('app.provider.client'))
        );

        // Register validators
        $this->set('app.validator.currency_response', new ApiCurrencyReaderResponseValidator($this->get('app.config')));

        // Register readers
        $this->set(
            'app.reader.currency',
            new ApiCurrencyReader(
                $this->get('app.factory.currency'),
                $this->get('app.validator.currency_response'),
                $this->get('app.config')
            )
        );
        $this->set('app.reader.input.file', new FileInputReader());

        // Register currency converter
//        $this->set('app.converter.currency', new CurrencyConverter($this->get('app.reader.currency')->getCurrencies()));
    }

    public function get(string $key): object
    {
        if (!$this->has($key)) {
            throw new UndefinedInstanceException();
        }

        return $this->instances[$key];
    }

    public function set(string $key, object $instance): void
    {
        if (!$this->has($key)) {
            $this->instances[$key] = $instance;
        }
    }

    public function has(string $key): bool
    {
        return !empty($this->instances[$key]);
    }
}
