<?php

declare(strict_types=1);

namespace App\CommissionTask\Converter;

use App\CommissionTask\Exception\Converter\NoBaseCurrencyException;
use App\CommissionTask\Exception\Converter\TooManyBaseCurrenciesException;
use App\CommissionTask\Model\Core\CurrencyInterface;
use App\CommissionTask\Repository\RepositoryInterface;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Money\Context\CustomContext;
use Brick\Money\CurrencyConverter as ExternalCurrencyConverter;
use Brick\Money\Exception\CurrencyConversionException;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\ExchangeRateProvider\BaseCurrencyProvider;
use Brick\Money\ExchangeRateProvider\ConfigurableProvider;
use Brick\Money\Money;

class CurrencyConverter implements CurrencyConverterInterface
{
    private const DEFAULT_SCALE = 2;

    protected RepositoryInterface $currencyRepository;

    public function __construct(RepositoryInterface $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * @throws CurrencyConversionException
     * @throws UnknownCurrencyException
     */
    public function convert(string $from, string $to, string $amount): BigDecimal
    {
        if ($from === $to) {
            return BigDecimal::of($amount);
        }

        return $this->getConverter()->convert(
            Money::of($amount, $from, null, RoundingMode::UP),
            $to,
            new CustomContext(self::DEFAULT_SCALE),
            RoundingMode::UP
        )->getAmount();
    }

    public function getBaseCurrency(): CurrencyInterface
    {
        /** @var array|CurrencyInterface[] $currencies */
        $currencies = $this->currencyRepository->all();
        $filtered = array_filter($currencies, static function (CurrencyInterface $currency) {
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
        /** @var array|CurrencyInterface[] $currencies */
        $currencies = $this->currencyRepository->all();

        /** @var CurrencyInterface $currency */
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
