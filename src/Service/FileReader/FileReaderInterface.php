<?php

declare(strict_types=1);

namespace App\Service\FileReader;

interface FileReaderInterface
{
    public function read(string $filePath, int $length = 1000): iterable;
}
