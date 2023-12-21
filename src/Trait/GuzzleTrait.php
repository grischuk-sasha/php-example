<?php

declare(strict_types=1);

namespace App\Trait;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Message;

trait GuzzleTrait
{
    private function buildExceptionMessage(ConnectException | RequestException $e): string
    {
        $request = Message::toString($e->getRequest());
        $response = null;
        if ($e instanceof RequestException && $e->hasResponse()) {
            $response = Message::toString($e->getResponse());
        }
        $msg = $e->getMessage() . PHP_EOL;
        $msg .= 'Request: '. PHP_EOL;
        $msg .= $request . PHP_EOL;
        $msg .= 'Response: '. PHP_EOL;
        $msg .= $response . PHP_EOL;

        return $msg;
    }
}
