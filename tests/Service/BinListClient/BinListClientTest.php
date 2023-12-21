<?php

declare(strict_types=1);

namespace BinListClient;

use App\Service\BinListClient\BinListClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class BinListClientTest extends TestCase
{
    #[DataProvider('providerGetBinListData')]
    public function testGetBinListData(array $data, array $expected): void
    {
        $mock = new MockHandler([
            $data['response'],
        ]);
        $handlerStack = HandlerStack::create($mock);
        $guzzleClient = new Client(['handler' => $handlerStack]);

        $binListClient = new BinListClient($guzzleClient);

        if (isset($expected['binListData'])) {
            $this->assertSame(
                $expected['binListData'],
                $binListClient->getBinListData('123123')->getCountryAlpha2Code()
            );
        }

        if (isset($expected['exception'])) {
            $this->expectException($expected['exception']);

            $binListClient->getBinListData('123123');
        }
    }

    public static function providerGetBinListData(): iterable
    {
        yield '200 ok' => [
            'data' => [
                'response' => new Response(200, [], json_encode([
                    'country' => [
                        'alpha2' => 'USA',
                    ]
                ])),
            ],
            'expected' => [
                'binListData' => 'USA'
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
