<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Logger;

use Mosaic\SDK\Logger;
use Mosaic\SDK\HttpClient;
use Mosaic\SDK\Struct;

/**
 * Base class for logger implementations
 *
 * @version $Revision$
 */
class Http extends Logger
{
    /**
     * HTTP Client
     *
     * @var HttpClient
     */
    protected $httpClient;

    public function __construct(
        HttpClient $httpClient
    ) {
        $this->httpClient = $httpClient;
    }

    /**
     * Log order
     *
     * @param Struct\Order $order
     * @return void
     */
    public function log(Struct\Order $order)
    {
        foreach (array('orderShop', 'providerShop', 'reservationId') as $property ) {
            if (!isset($order->$property)) {
                throw new \InvalidArgumentException("Required order property \$$property not set.");
            }
        }

        $response = $this->httpClient->request(
            'POST',
            '/log',
            json_encode($order),
            array(
                'Content-Type: application/json',
            )
        );

        if ($response->status >= 400) {
            throw new \RuntimeException("Logging failed.");
        }

        return;
    }
}
