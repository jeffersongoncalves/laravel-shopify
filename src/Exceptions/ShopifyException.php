<?php

namespace JeffersonGoncalves\Shopify\Exceptions;

use RuntimeException;

/**
 * Raised when the Shopify Admin API answers with a non-2xx HTTP status, when a
 * GraphQL response carries an `errors` array, or when the shop domain / access
 * token needed for a call is not configured. Carries the API's error message
 * and the HTTP status code (0 for configuration errors).
 */
class ShopifyException extends RuntimeException
{
    public function __construct(string $message, public readonly int $statusCode = 0)
    {
        parent::__construct($message, $statusCode);
    }
}
