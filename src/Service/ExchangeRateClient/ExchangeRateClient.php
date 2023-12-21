<?php

declare(strict_types=1);

namespace App\Service\ExchangeRateClient;

use App\Trait\GuzzleTrait;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use RuntimeException;

class ExchangeRateClient implements ExchangeRateClientInterface
{
    use GuzzleTrait;

    private const BASE_URL = 'http://api.exchangeratesapi.io/latest';
    private const ACCESS_KEY = '';

    public function __construct(
        private readonly ClientInterface $httpClient = new Client(),
    ) {}

    public function getRateData(): ExchangeRateDataInterface
    {
        try {
            $queryData = http_build_query(['access_key' => self::ACCESS_KEY]);

            $request = new Request(
                'GET',
                self::BASE_URL . '?' . $queryData
            );
            $response = $this->httpClient->send($request);
            $data = json_decode($response->getBody()->getContents(), true);

            if ($data === null || $data['success'] === false) {
                throw new BadResponseException(
                    'Some error occurred during ExchangeRateClient request',
                    $request,
                    $response
                );
            }

            return new ExchangeRateData($data);
        } catch (ConnectException | RequestException $e) {
            throw new RuntimeException($this->buildExceptionMessage($e), 0, $e);
        }
    }
}
