<?php

declare(strict_types=1);

namespace App\Service\BinListClient;

use App\Trait\GuzzleTrait;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use RuntimeException;

class BinListClient implements BinListClientInterface
{
    use GuzzleTrait;

    private const BASE_URL = 'https://lookup.binlist.net/';

    public function __construct(
        private readonly ClientInterface $httpClient = new Client(),
    ) {}

    public function getBinListData($id): BinListDataInterface
    {
        try {
            $request = new Request(
                'GET',
                self::BASE_URL . $id
            );
            $response = $this->httpClient->send($request);
            $data = json_decode($response->getBody()->getContents(), true);

            if ($data === null) {
                throw new BadResponseException(
                    'Some error occurred during BinListClient request',
                    $request,
                    $response
                );
            }

            return new BinListData($data);
        } catch (ConnectException | RequestException $e) {
            throw new RuntimeException($this->buildExceptionMessage($e), 0, $e);
        }
    }
}
