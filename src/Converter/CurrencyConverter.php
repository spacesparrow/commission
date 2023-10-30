<?php

declare(strict_types=1);

namespace App\CommissionTask\Converter;

use App\CommissionTask\Exception\Converter\NoBaseCurrencyException;
use App\CommissionTask\Exception\Converter\TooManyBaseCurrenciesException;
use App\CommissionTask\Model\Core\Currency;
use App\CommissionTask\Reader\Currency\CurrencyReaderInterface;
use App\CommissionTask\Storage\StorageInterface;
use Brick\Math\BigDecimal;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;
use Brick\Money\Context\CustomContext;
use Brick\Money\CurrencyConverter as ExternalCurrencyConverter;
use Brick\Money\Exception\CurrencyConversionException;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\ExchangeRateProvider\BaseCurrencyProvider;
use Brick\Money\ExchangeRateProvider\ConfigurableProvider;
use Brick\Money\Money;

class CurrencyConverter
{
    private const DEFAULT_SCALE = 2;

    public function __construct(
        private readonly StorageInterface $storage,
        private readonly CurrencyReaderInterface $currencyReader
    ) {
    }

    /**
     * @throws CurrencyConversionException
     * @throws UnknownCurrencyException
     * @throws MathException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     */
    public function convert(string $from, string $to, string $amount): BigDecimal
    {
        if ($from === $to) {
            return BigDecimal::of($amount);
        }

        if (empty($this->storage->all(StorageInterface::PARTITION_CURRENCIES))) {
            $this->currencyReader->read();
        }

        return $this->getConverter()->convert(
            Money::of($amount, $from, null, RoundingMode::UP),
            $to,
            new CustomContext(self::DEFAULT_SCALE),
            RoundingMode::UP
        )->getAmount();
    }

    private function getBaseCurrency(): Currency
    {
        /** @var array|Currency[] $currencies */
        $currencies = $this->storage->all(StorageInterface::PARTITION_CURRENCIES);
        $filtered = array_filter($currencies, static function (Currency $currency) {
            return $currency->isBase();
        });

        if (empty($filtered)) {
            throw new NoBaseCurrencyException();
        }

        if (count($filtered) > 1) {
            throw new TooManyBaseCurrenciesException();
        }

        return array_pop($filtered);
    }

    private function getConfiguredExchangeProvider(): ConfigurableProvider
    {
        $baseCurrency = $this->getBaseCurrency();
        $provider = new ConfigurableProvider();
        /** @var array|Currency[] $currencies */
        $currencies = $this->storage->all(StorageInterface::PARTITION_CURRENCIES);

        /** @var Currency $currency */
        foreach ($currencies as $currency) {
            $provider->setExchangeRate($baseCurrency->getCode(), $currency->getCode(), $currency->getRate());
        }

        return $provider;
    }

    private function getConverter(): ExternalCurrencyConverter
    {
        $exchangeProvider = $this->getConfiguredExchangeProvider();
        $baseCurrencyProvider = new BaseCurrencyProvider($exchangeProvider, $this->getBaseCurrency()->getCode());

        return new ExternalCurrencyConverter($baseCurrencyProvider);
    }
}
