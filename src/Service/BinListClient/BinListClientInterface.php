<?php

declare(strict_types=1);

namespace App\Service\BinListClient;

interface BinListClientInterface
{
    public function getBinListData($id): BinListDataInterface;
}