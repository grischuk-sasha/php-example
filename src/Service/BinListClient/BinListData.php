<?php

declare(strict_types=1);

namespace App\Service\BinListClient;

use RuntimeException;

class BinListData implements BinListDataInterface
{
    public function __construct(
        private readonly array $data
    ) {}

    public function getCountryAlpha2Code(): string
    {
        if (empty($data = $this->data['country']['alpha2'])) {
            throw new RuntimeException('Invalid country alpha2 code');
        }

        return $data;
    }
}
