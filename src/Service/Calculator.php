<?php

declare(strict_types=1);

namespace App\Service;

use App\Enum\CountryEuAlpha2CodesEnum;
use App\Enum\CurrencyEnum;
use App\Service\BinListClient\BinListClient;
use App\Service\BinListClient\BinListClientInterface;
use App\Service\ExchangeRateClient\ExchangeRateClient;
use App\Service\ExchangeRateClient\ExchangeRateClientInterface;
use App\Service\FileReader\FileReader;
use App\Service\FileReader\FileReaderInterface;

class Calculator
{
    public function __construct(
        private readonly FileReaderInterface $fileReader = new FileReader(),
        private readonly BinListClientInterface $binListClient = new BinListClient(),
        private readonly ExchangeRateClientInterface $exchangeRateClient = new ExchangeRateClient(),
    ) {}

    public function calculateCommission(string $filePath): iterable
    {
        foreach ($this->fileReader->read($filePath) as $readData) {
            list('bin' => $bin, 'amount' => $amount, 'currency' => $currency) = $readData;

            $exchangeRate = $this->exchangeRateClient->getRateData()->getRate($currency);

            if ($currency === CurrencyEnum::EUR->value || $exchangeRate == 0) {
                $amountFixed = $amount;
            } else {
                $amountFixed = $amount / $exchangeRate;
            }

            yield round(
                $amountFixed * ($this->isEuCountry($bin) ? 0.01 : 0.02),
                2
            );
        }
    }

    private function isEuCountry(string $bin): bool
    {
        $data = $this->binListClient->getBinListData($bin);

        return in_array(
            $data->getCountryAlpha2Code(),
            array_column(CountryEuAlpha2CodesEnum::cases(), 'value')
        );
    }
}
