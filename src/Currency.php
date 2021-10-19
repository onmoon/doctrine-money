<?php

declare(strict_types=1);

namespace OnMoon\Money;

use Money\Currencies;
use Money\Currency as LibCurrency;

class Currency
{
    /** @psalm-var non-empty-string */
    private string $code;

    /**
     * @psalm-param  non-empty-string $code
     */
    final private function __construct(string $code)
    {
        $this->code = $code;
    }

    /**
     * @psalm-param  non-empty-string $code
     */
    final public static function create(string $code): self
    {
        return new static($code);
    }

    /**
     * @psalm-return non-empty-string
     */
    final public function getCode(): string
    {
        return $this->getLibCurrency()->getCode();
    }

    final public function equals(self $other): bool
    {
        return $this->getLibCurrency()->equals($other->getLibCurrency());
    }

    final public function isAvailableWithin(Currencies $currencies): bool
    {
        return $currencies->contains($this->getLibCurrency());
    }

    final public function jsonSerialize(): string
    {
        return $this->getLibCurrency()->jsonSerialize();
    }

    public function __toString(): string
    {
        return (string) $this->getLibCurrency();
    }

    private function getLibCurrency(): LibCurrency
    {
        return new LibCurrency($this->code);
    }
}
