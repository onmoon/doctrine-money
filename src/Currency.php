<?php

declare(strict_types=1);

namespace OnMoon\Money;

use Money\Currencies;
use Money\Currency as LibCurrency;

class Currency
{
    /** @var string */
    private $code;

    /** @var int */
    private $subunits;

    final private function __construct(string $code, int $subunits)
    {
        $this->code     = $code;
        $this->subunits = $subunits;
    }

    final public static function create(string $code, int $subunits) : self
    {
        return new static($code, $subunits);
    }

    final public function getCode() : string
    {
        return $this->getLibCurrency()->getCode();
    }

    final public function getSubUnits() : int
    {
        return $this->subunits;
    }

    final public function equals(self $other) : bool
    {
        return $this->getLibCurrency()->equals($other->getLibCurrency());
    }

    final public function isAvailableWithin(Currencies $currencies) : bool
    {
        return $currencies->contains($this->getLibCurrency());
    }

    final public function jsonSerialize() : string
    {
        return $this->getLibCurrency()->jsonSerialize();
    }

    public function __toString() : string
    {
        return (string) $this->getLibCurrency();
    }

    private function getLibCurrency() : LibCurrency
    {
        return new LibCurrency($this->code);
    }
}
