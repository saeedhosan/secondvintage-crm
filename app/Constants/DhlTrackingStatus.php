<?php

declare(strict_types=1);

namespace App\Constants;

/**
 * DHL Tracking Status Constants
 * 
 * Defines DHL API status codes and their corresponding
 * internal application status values.
 */
final class DhlTrackingStatus
{
    /** DHL pre-transit status */
    public const string PRE_TRANSIT = 'pre-transit';
    
    /** DHL in-transit status */
    public const string TRANSIT = 'transit';
    
    /** DHL delivered status */
    public const string DELIVERED = 'delivered';
    
    /** DHL delivery failure status */
    public const string FAILURE = 'failure';
    
    /** DHL unknown status */
    public const string UNKNOWN = 'unknown';

    /** Internal preparing status */
    public const string STATUS_PREPARING = 'preparing';
    
    /** Internal in-transit status */
    public const string STATUS_IN_TRANSIT = 'in_transit';
    
    /** Internal delivered status */
    public const string STATUS_DELIVERED = 'delivered';
    
    /** Internal shipped status */
    public const string STATUS_SHIPPED = 'shipped';

    /** Maps DHL status codes to internal status values */
    public const array STATUS_MAP = [
        self::PRE_TRANSIT => self::STATUS_PREPARING,
        self::TRANSIT => self::STATUS_IN_TRANSIT,
        self::DELIVERED => self::STATUS_DELIVERED,
        self::FAILURE => self::STATUS_SHIPPED,
        self::UNKNOWN => self::STATUS_SHIPPED,
    ];
}