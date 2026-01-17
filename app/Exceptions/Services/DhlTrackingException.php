<?php

declare(strict_types=1);

namespace App\Exceptions\Services;

use App\Exceptions\BaseException;
use Exception;

class DhlTrackingException extends BaseException
{
    public const ERROR_INVALID_TRACKING_NUMBER = 'invalid_tracking_number';
    public const ERROR_API_REQUEST_FAILED = 'api_request_failed';
    public const ERROR_INVALID_RESPONSE = 'invalid_response';
    public const ERROR_NETWORK_ERROR = 'network_error';

    public function __construct(
        string $message = '',
        protected string $errorCode = self::ERROR_API_REQUEST_FAILED,
        int $statusCode = 500,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getStatusCode(): int
    {
        return $this->getCode();
    }

    public static function invalidTrackingNumber(string $trackingNumber): self
    {
        return new self(
            "Invalid or empty DHL tracking number: {$trackingNumber}",
            self::ERROR_INVALID_TRACKING_NUMBER,
            400
        );
    }

    public static function apiRequestFailed(string $trackingNumber, int $httpStatus, string $response): self
    {
        return new self(
            "DHL API request failed for tracking number {$trackingNumber}. HTTP Status: {$httpStatus}",
            self::ERROR_API_REQUEST_FAILED,
            502
        );
    }

    public static function invalidResponse(string $trackingNumber): self
    {
        return new self(
            "Invalid response from DHL API for tracking number: {$trackingNumber}",
            self::ERROR_INVALID_RESPONSE,
            502
        );
    }

    public static function networkError(string $trackingNumber, Exception $previous): self
    {
        return new self(
            "Network error when calling DHL API for tracking number: {$trackingNumber}",
            self::ERROR_NETWORK_ERROR,
            503,
            $previous
        );
    }
}