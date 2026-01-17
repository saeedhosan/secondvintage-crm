<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\DhlTrackingService;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

final class DhlTrackingServiceTest extends TestCase
{
    private DhlTrackingService $service;
    private const string TEST_API_KEY = 'test-api-key';
    private const string VALID_TRACKING_NUMBER = '1234567890';
    private const string INVALID_TRACKING_NUMBER = 'INVALID';

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('services.dhl.api_key', self::TEST_API_KEY);
        $this->service = new DhlTrackingService();
    }

    public function test_service_can_be_instantiated(): void
    {
        $this->assertInstanceOf(DhlTrackingService::class, $this->service);
    }

    public function test_get_tracking_info_returns_null_for_empty_tracking_number(): void
    {
        $result = $this->service->getTrackingInfo('');

        $this->assertNull($result);
    }

    public function test_get_tracking_info_returns_null_for_api_failure(): void
    {
        Http::fake([
            'api-eu.dhl.com/*' => Http::response([], 500),
        ]);

        $result = $this->service->getTrackingInfo(self::VALID_TRACKING_NUMBER);

        $this->assertNull($result);
    }

    public function test_get_tracking_info_returns_null_for_not_found_response(): void
    {
        Http::fake([
            'api-eu.dhl.com/*' => Http::response([
                'shipments' => []
            ], 404),
        ]);

        $result = $this->service->getTrackingInfo(self::INVALID_TRACKING_NUMBER);

        $this->assertNull($result);
    }

    public function test_get_tracking_info_returns_parsed_data_for_successful_response(): void
    {
        $mockResponse = [
            'shipments' => [
                [
                    'status' => [
                        'statusCode' => 'delivered',
                        'description' => 'Delivered'
                    ],
                    'events' => [
                        [
                            'location' => [
                                'address' => [
                                    'addressLocality' => 'Berlin',
                                    'countryCode' => 'DE'
                                ]
                            ],
                            'description' => 'Delivered'
                        ]
                    ]
                ]
            ]
        ];

        Http::fake([
            'api-eu.dhl.com/*' => Http::response($mockResponse, 200),
        ]);

        $result = $this->service->getTrackingInfo(self::VALID_TRACKING_NUMBER);

        $expected = [
            'status' => 'delivered',
            'location' => 'Berlin, DE',
            'events' => $mockResponse['shipments'][0]['events']
        ];

        $this->assertEquals($expected, $result);
    }

    public function test_get_tracking_info_handles_unknown_status_gracefully(): void
    {
        $mockResponse = [
            'shipments' => [
                [
                    'status' => [
                        'statusCode' => 'unknown_status',
                        'description' => 'Unknown Status'
                    ],
                    'events' => []
                ]
            ]
        ];

        Http::fake([
            'api-eu.dhl.com/*' => Http::response($mockResponse, 200),
        ]);

        $result = $this->service->getTrackingInfo(self::VALID_TRACKING_NUMBER);

        $this->assertEquals('shipped', $result['status']);
    }

    public function test_get_tracking_info_handles_missing_location(): void
    {
        $mockResponse = [
            'shipments' => [
                [
                    'status' => [
                        'statusCode' => 'transit',
                        'description' => 'In Transit'
                    ],
                    'events' => []
                ]
            ]
        ];

        Http::fake([
            'api-eu.dhl.com/*' => Http::response($mockResponse, 200),
        ]);

        $result = $this->service->getTrackingInfo(self::VALID_TRACKING_NUMBER);

        $this->assertEquals('in_transit', $result['status']);
        $this->assertNull($result['location']);
    }
}
