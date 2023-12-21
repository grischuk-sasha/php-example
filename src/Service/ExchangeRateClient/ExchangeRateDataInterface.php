<?php

declare(strict_types=1);

namespace App\Service\ExchangeRateClient;

interface ExchangeRateDataInterface
{
    public function getRate(string $currency): float;
}
