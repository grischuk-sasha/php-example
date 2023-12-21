<?php

declare(strict_types=1);

namespace ExchangeRateClient;

use App\Service\ExchangeRateClient\ExchangeRateClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ExchangeRateClientTest extends TestCase
{
    #[DataProvider('providerGetRateData')]
    public function testGetRateData(array $data, array $expected): void
    {
        $mock = new MockHandler([
            $data['response'],
        ]);
        $handlerStack = HandlerStack::create($mock);
        $guzzleClient = new Client(['handler' => $handlerStack]);

        $binListClient = new ExchangeRateClient($guzzleClient);

        if (isset($expected['rateData'])) {
            $this->assertSame(
                $expected['rateData'],
                $binListClient->getRateData()->getRate('EUR')
            );
        }

        if (isset($expected['exception'])) {
            $this->expectException($expected['exception']);

            $binListClient->getRateData();
        }
    }

    public static function providerGetRateData(): iterable
    {
        yield '200 ok' => [
            'data' => [
                'response' => new Response(200, [], json_encode([
                    'rates' => [
                        'EUR' => 0.943,
                    ]
                ])),
            ],
            'expected' => [
                'rateData' => 0.943
            ]
        ];

        yield '200 but not json' => [
            'data' => [
                'response' => new Response(200, [], 'test'),
            ],
            'expected' => [
                'exception' => RuntimeException::class
            ]
        ];

        yield 'not 200' => [
            'data' => [
                'response' => new Response(500, [], 'error'),
            ],
            'expected' => [
                'exception' => RuntimeException::class
            ]
        ];
    }

}