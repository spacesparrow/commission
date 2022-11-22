<?php

declare(strict_types=1);

namespace App\CommissionTask\Kernel;

use App\CommissionTask\Converter\CurrencyConverter;
use App\CommissionTask\Exception\Kernel\UndefinedInstanceException;
use App\CommissionTask\Factory\Core\CurrencyFactory;
use App\CommissionTask\Reader\Currency\ApiCurrencyReader;

class Container implements ContainerInterface
{
    protected array $instances = [];

    public function init(): void
    {
        $this->set('app.factory.currency', new CurrencyFactory());
        $this->set('app.reader.currency', new ApiCurrencyReader($this->get('app.factory.currency')));
        $this->set('app.converter.currency', new CurrencyConverter($this->get('app.reader.currency')->getCurrencies()));
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
