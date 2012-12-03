<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Service;

use Mosaic\SDK\HttpClient;
use Mosaic\SDK\Gateway;

/**
 * Verification service
 *
 * @version $Revision$
 */
class Verification
{
    /**
     * HTTP Client
     *
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * Shop cinfiguration gateway
     *
     * @var Gateway\ShopConfiguration
     */
    protected $config;

    public function __construct(
        HttpClient $httpClient,
        Gateway\ShopConfiguration $config
    ) {
        $this->httpClient = $httpClient;
        $this->config = $config;
    }

    /**
     * Verify the shops API key and stores the shopId in the response for
     * future use
     *
     * @param string $apiKey
     * @param string $apiEndpointUrl
     * @return void
     */
    public function verify($apiKey, $apiEndpointUrl)
    {
        $response = $this->httpClient->request(
            'POST',
            '/api/verify',
            json_encode(
                array(
                    'apiKey' => $apiKey,
                    'apiEndpointUrl' => $apiEndpointUrl,
                )
            ),
            array(
                'Content-Type: application/json',
            )
        );

        $return = json_decode($response->body);
        if ($response->status >= 400) {
            throw new \RuntimeException(
                "Verification failed: " . $return->error
            );
        }

        $this->config->setShopId($return->shopId);
        return;
    }
}
