<?php

declare(strict_types=1);

namespace App\Service\ExchangeRateClient;

use RuntimeException;

class ExchangeRateData implements ExchangeRateDataInterface
{
    public function __construct(
        private readonly array $data
    ) {}

    public function getRate(string $currency): float
    {
        if (!isset($this->data['rates'][$currency])) {
            throw new RuntimeException('Invalid rate for the currency: ' . $currency);
        }

        return $this->data['rates'][$currency];
    }
}