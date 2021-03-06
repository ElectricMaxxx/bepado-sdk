<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK\HttpClient;

use Bepado\SDK\Struct\AuthenticationToken;

/**
 * Deactivate the security for request signing.
 *
 * WARNING: This class is not meant to be used in production.
 */
class NoSecurityRequestSigner implements RequestSigner
{
    public function signRequest($shopId, $body)
    {
        return array();
    }

    public function verifyRequest($body, array $headers)
    {
        return new AuthenticationToken(array('authenticated' => true));
    }
}
