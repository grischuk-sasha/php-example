<?php

declare(strict_types=1);

namespace App\Service\ExchangeRateClient;

interface ExchangeRateClientInterface
{
    public function getRateData(): ExchangeRateDataInterface;
}
