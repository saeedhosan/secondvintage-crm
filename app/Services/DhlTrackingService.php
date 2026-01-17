<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\DhlTrackingStatus;
use App\Exceptions\Services\DhlTrackingException;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * DHL Tracking Service - Integrates with DHL API for shipment tracking
 * 
 * Provides real-time tracking information for DHL shipments with
 * proper error handling and status mapping.
 */
class DhlTrackingService
{
    private readonly ?string $apiKey;
    
    /** Base URL for DHL tracking API */
    private const string BASE_URL = 'https://api-eu.dhl.com/track/shipments';

    /**
     * Make HTTP request to DHL API.
     */
    private function makeApiRequest(string $trackingNumber): Response
    {
        return Http::withHeaders([
            'DHL-API-Key' => $this->apiKey,
        ])->get(self::BASE_URL, [
            'trackingNumber' => $trackingNumber,
        ]);
    }

    /**
     * Parse DHL API response into standardized format.
     */
    private function parseTrackingResponse(array $response): array
    {
        $shipments = $response['shipments'] ?? [];

        if (empty($shipments)) {
            return $this->createEmptyTrackingResponse();
        }

        $shipment = $shipments[0];
        $status = $shipment['status'] ?? [];
        $events = $shipment['events'] ?? [];

        return [
            'status' => $this->mapDhlStatus($status['statusCode'] ?? null),
            'location' => $this->extractLatestLocation($events),
            'events' => $events,
        ];
    }

    /**
     * Create empty tracking response for invalid/unknown shipments.
     */
    private function createEmptyTrackingResponse(): array
    {
        return [
            'status' => null,
            'location' => null,
            'events' => [],
        ];
    }

    /**
     * Extract location information from latest tracking event.
     */
    private function extractLatestLocation(array $events): ?string
    {
        if (empty($events)) {
            return null;
        }

        $latestEvent = $events[0];
        $location = $latestEvent['location'] ?? [];
        $address = $location['address'] ?? [];

        $locationParts = array_filter([
            $address['addressLocality'] ?? null,
            $address['countryCode'] ?? null,
        ]);

        return !empty($locationParts) ? implode(', ', $locationParts) : null;
    }

    /**
     * Map DHL status codes to internal status values.
     */
    private function mapDhlStatus(?string $dhlStatus): ?string
    {
        if (empty($dhlStatus)) {
            return null;
        }

        return DhlTrackingStatus::STATUS_MAP[$dhlStatus] ?? DhlTrackingStatus::STATUS_SHIPPED;
    }

    /**
     * Log DHL API errors for debugging.
     */
    private function logApiError(string $trackingNumber, Response $response): void
    {
        Log::warning('DHL API request failed', [
            'tracking_number' => $trackingNumber,
            'status' => $response->status(),
            'response' => $response->body(),
        ]);
    }

    /**
     * Log DHL API exceptions for debugging.
     */
    private function logApiException(string $trackingNumber, Exception $e): void
    {
        Log::error('DHL API request exception', [
            'tracking_number' => $trackingNumber,
            'error' => $e->getMessage(),
        ]);
    }

    /**
     * Get tracking information for a DHL shipment.
     *
     * @param string $trackingNumber DHL tracking number
     * @return array{status: string|null, location: string|null, events: array}|null Tracking information
     * @throws DhlTrackingException When tracking number is invalid or API request fails
     */
    public function getTrackingInfo(string $trackingNumber): ?array
    {
        if (empty($trackingNumber)) {
            throw DhlTrackingException::invalidTrackingNumber($trackingNumber);
        }

        try {
            $response = $this->makeApiRequest($trackingNumber);

            if (!$response->successful()) {
                $this->logApiError($trackingNumber, $response);
                throw DhlTrackingException::apiRequestFailed(
                    $trackingNumber,
                    $response->status(),
                    $response->body()
                );
            }

            return $this->parseTrackingResponse($response->json());
        } catch (DhlTrackingException $e) {
            throw $e;
        } catch (Exception $e) {
            $this->logApiException($trackingNumber, $e);
            throw DhlTrackingException::networkError($trackingNumber, $e);
        }
    }

    private function makeApiRequest(string $trackingNumber): Response
    {
        return Http::withHeaders([
            'DHL-API-Key' => $this->apiKey,
        ])->get(self::BASE_URL, [
            'trackingNumber' => $trackingNumber,
        ]);
    }

    private function parseTrackingResponse(array $response): array
    {
        $shipments = $response['shipments'] ?? [];

        if (empty($shipments)) {
            return $this->createEmptyTrackingResponse();
        }

        $shipment = $shipments[0];
        $status = $shipment['status'] ?? [];
        $events = $shipment['events'] ?? [];

        return [
            'status' => $this->mapDhlStatus($status['statusCode'] ?? null),
            'location' => $this->extractLatestLocation($events),
            'events' => $events,
        ];
    }

    private function createEmptyTrackingResponse(): array
    {
        return [
            'status' => null,
            'location' => null,
            'events' => [],
        ];
    }

    private function extractLatestLocation(array $events): ?string
    {
        if (empty($events)) {
            return null;
        }

        $latestEvent = $events[0];
        $location = $latestEvent['location'] ?? [];
        $address = $location['address'] ?? [];

        $locationParts = array_filter([
            $address['addressLocality'] ?? null,
            $address['countryCode'] ?? null,
        ]);

        return !empty($locationParts) ? implode(', ', $locationParts) : null;
    }

    private function mapDhlStatus(?string $dhlStatus): ?string
    {
        if (empty($dhlStatus)) {
            return null;
        }

        return DhlTrackingStatus::STATUS_MAP[$dhlStatus] ?? DhlTrackingStatus::STATUS_SHIPPED;
    }

    private function logApiError(string $trackingNumber, Response $response): void
    {
        Log::warning('DHL API request failed', [
            'tracking_number' => $trackingNumber,
            'status' => $response->status(),
            'response' => $response->body(),
        ]);
    }

    private function logApiException(string $trackingNumber, Exception $e): void
    {
        Log::error('DHL API request exception', [
            'tracking_number' => $trackingNumber,
            'error' => $e->getMessage(),
        ]);
    }
}
