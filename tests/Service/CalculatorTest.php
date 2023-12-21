<?php

declare(strict_types=1);

use App\Service\BinListClient\BinListClientInterface;
use App\Service\BinListClient\BinListData;
use App\Service\Calculator;
use App\Service\ExchangeRateClient\ExchangeRateClientInterface;
use App\Service\ExchangeRateClient\ExchangeRateData;
use App\Service\FileReader\FileReaderInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    #[DataProvider('providerCalculateCommission')]
    public function testCalculateCommission(array $data, array $expected): void
    {
        $calculator = new Calculator(
            $this->mockFileReader($data['fileReaderData']),
            $this->mockBinListClient($data['binListClientData']),
            $this->mockExchangeRateClient($data['exchangeRateClientData']),
        );

        foreach ($calculator->calculateCommission('some-file.txt') as $commission) {
            if (isset($expected['commission'])) {
                $this->assertSame($commission, $expected['commission']);
            }
        }
    }

    public static function providerCalculateCommission(): iterable
    {
        yield 'EUR currency and not EU country' => [
            'data' => [
                'fileReaderData' => [
                    [
                        "bin" => "4745030",
                        "amount" => "2000.00",
                        "currency" => "EUR"
                    ],
                ],
                'binListClientData' => [
                    'country' => [
                        'alpha2' => 'USA',
                    ]
                ],
                'exchangeRateClientData' => [
                    'rates' => [
                        'EUR' => 0.943,
                    ]
                ],
            ],
            'expected' => [
                'commission' => 40.0
            ]
        ];

        yield 'EUR currency and EU country' => [
            'data' => [
                'fileReaderData' => [
                    [
                        "bin" => "4745030",
                        "amount" => "2000.00",
                        "currency" => "EUR"
                    ],
                ],
                'binListClientData' => [
                    'country' => [
                        'alpha2' => 'DE',
                    ]
                ],
                'exchangeRateClientData' => [
                    'rates' => [
                        'EUR' => 0.943,
                    ]
                ],
            ],
            'expected' => [
                'commission' => 20.0
            ]
        ];

        yield 'not EUR currency and not EU country' => [
            'data' => [
                'fileReaderData' => [
                    [
                        "bin" => "4745030",
                        "amount" => "2000.00",
                        "currency" => "USD"
                    ],
                ],
                'binListClientData' => [
                    'country' => [
                        'alpha2' => 'USA',
                    ]
                ],
                'exchangeRateClientData' => [
                    'rates' => [
                        'USD' => 0.943,
                    ]
                ],
            ],
            'expected' => [
                'commission' => 42.42
            ]
        ];

        yield 'not EUR currency and EU country' => [
            'data' => [
                'fileReaderData' => [
                    [
                        "bin" => "4745030",
                        "amount" => "2000.00",
                        "currency" => "AUD"
                    ],
                ],
                'binListClientData' => [
                    'country' => [
                        'alpha2' => 'DE',
                    ]
                ],
                'exchangeRateClientData' => [
                    'rates' => [
                        'AUD' => 0.564,
                    ]
                ],
            ],
            'expected' => [
                'commission' => 35.46
            ]
        ];
    }

    private function mockFileReader(array $data)
    {
        $mock = $this->createMock(FileReaderInterface::class);
        $mock->method('read')->willReturn($data);

        return $mock;
    }

    private function mockBinListClient(array $data)
    {
        $binListData = new BinListData($data);

        $mock = $this->createMock(BinListClientInterface::class);
        $mock->method('getBinListData')->willReturn($binListData);

        return $mock;
    }

    private function mockExchangeRateClient(array $data)
    {
        $data = new ExchangeRateData($data);

        $mock = $this->createMock(ExchangeRateClientInterface::class);
        $mock->method('getRateData')->willReturn($data);

        return $mock;
    }
}
