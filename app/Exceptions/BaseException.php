<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * Base Exception - Foundation for all custom exceptions
 * 
 * Provides a common interface for application-specific exceptions
 * with error codes and HTTP status codes.
 */
abstract class BaseException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the error code for logging/identification.
     */
    abstract public function getErrorCode(): string;
    
    /**
     * Get the HTTP status code for response.
     */
    abstract public function getStatusCode(): int;
}