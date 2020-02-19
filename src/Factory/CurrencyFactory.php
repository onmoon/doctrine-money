<?php

declare(strict_types=1);

namespace OnMoon\Money\Factory;

use Money\Currencies;
use Money\Currency as LibCurrency;
use OnMoon\Money\Currency;
use OnMoon\Money\Factory\Exception\CannotCreateCurrency;
use function array_key_exists;
use function array_map;
use function iterator_to_array;

final class CurrencyFactory
{
    /**
     * @var mixed[]
     * @psalm-var array<string, Currencies>
     */
    private $contexts = [];

    public function __construct(?Currencies $currencies = null)
    {
        if ($currencies === null) {
            return;
        }

        $this->contexts['global'] = $currencies;
    }

    public function registerContext(string $context, Currencies $currencies) : void
    {
        $this->contexts[$context] = $currencies;
    }

    public function create(string $code, string $context = 'global') : Currency
    {
        $libCurrency = new LibCurrency($code);

        if (! array_key_exists($context, $this->contexts) ||
            ! $this->contexts[$context]->contains($libCurrency)
        ) {
            throw CannotCreateCurrency::becauseCurrencyCodeNotAllowed($code, ...$this->getAllowedCurrencies($context));
        }

        return Currency::create($code, $this->contexts[$context]->subunitFor($libCurrency));
    }

    /**
     * @return string[]
     */
    public function getAllowedCurrencies(string $context = 'global') : array
    {
        return array_key_exists($context, $this->contexts) ?
            array_map(
                static function (LibCurrency $currency) : string {
                    return (string) $currency;
                },
                iterator_to_array($this->contexts[$context]->getIterator())
            ) :
            [];
    }
}
