<?php

declare(strict_types=1);

namespace App\Service\BinListClient;

interface BinListDataInterface
{
    public function getCountryAlpha2Code(): string;
}