<?php

declare(strict_types=1);

namespace App\Service\FileReader;

use RuntimeException;

class FileReader implements FileReaderInterface
{
    public function read(string $filePath, int $length = 1000): iterable
    {
        $file = fopen($filePath, "r");
        if ($file === false) {
            throw new RuntimeException("Cannot open file: {$filePath}");
        }

        try {
            while (($data = fgets($file, $length)) !== false) {
                yield json_decode($data, true);
            }
        } finally {
            fclose($file);
        }
    }
}