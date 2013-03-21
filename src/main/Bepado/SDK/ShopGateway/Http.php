<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * @version $Revision$
 */

namespace Bepado\SDK\ShopGateway;

use Bepado\SDK\ShopGateway;
use Bepado\SDK\Struct;
use Bepado\SDK\HttpClient;
use Bepado\Common\Rpc\Marshaller\CallMarshaller;
use Bepado\Common\Rpc\Marshaller\CallUnmarshaller;

/**
 * Shop gateway HTTP implementation
 *
 * Gateway to interact with other shops
 *
 * @version $Revision$
 */
class Http extends ShopGateway
{
    /**
     * HTTP Client
     *
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * Call marshaller
     *
     * @var Rpc\Marshaller\CallMarshaller
     */
    protected $marshaller;

    /**
     * Call unmarshaller
     *
     * @var Rpc\Marshaller\CallUnmarshaller
     */
    protected $unmarshaller;

    /**
     * Configuration of the provider shop
     *
     * @var Bepado\SDK\Struct\ShopConfiguration
     */
    protected $providerShopConfig;

    /**
     * @param Bepado\SDK\HttpClient $httpClient
     * @param Bepado\Common\Rpc\Marshaller\CallMarshaller $marshaller
     * @param Bepado\Common\Rpc\Marshaller\CallUnmarshaller $unmarshaller
     * @param Bepado\SDK\Gateway\ShopConfiguration $providerShopConfig
     */
    public function __construct(HttpClient $httpClient, CallMarshaller $marshaller, CallUnmarshaller $unmarshaller, ShopConfiguration $providerShopConfig)
    {
        $this->httpClient = $httpClient;
        $this->marshaller = $marshaller;
        $this->unmarshaller = $unmarshaller;
        $this->providerShopConfig = $providerShopConfig;
    }

    /**
     * Check order in shop
     *
     * Verifies, if all products in the given order still have the same price
     * and availability.
     *
     * Returns true on success, or an array of Struct\Change with updates for
     * the requested products.
     *
     * @param Struct\Order $order
     * @return mixed
     */
    public function checkProducts(Struct\Order $order)
    {
        $call = new RpcCall(
            array(
                'service' => 'transaction',
                'command' => 'checkProducts',
                'arguments' => array($order),
            )
        );
        $marshalledCall = $this->marshaller->marshal($call);

        $httpResponse = $this->request('POST', $this->providerShopConfig->serviceEndpoint, $marshalledCall);

        // TODO: Check status
        $responseCall = $this->unmarshaller->unmarshaller($httpResponse->body);
        return $responseCall->arguments[0];
    }

    /**
     * Reserve order in remote shop
     *
     * Products SHOULD be reserved and not be sold out while bing reserved.
     * Reservation may be cancelled after sufficient time has passed.
     *
     * Returns a reservationId on success, or an array of Struct\Change with
     * updates for the requested products.
     *
     * @param Struct\Order
     * @return mixed
     */
    public function reserveProducts(Struct\Order $order)
    {
        throw new \RuntimeException("@TODO: Implement");
    }

    /**
     * Buy order associated with reservation in the remote shop.
     *
     * Returns true on success, or a Struct\Message on failure. SHOULD never
     * fail.
     *
     * @param string $reservationId
     * @return mixed
     */
    public function buy($reservationId)
    {
        throw new \RuntimeException("@TODO: Implement");
    }

    /**
     * Confirm a reservation in the remote shop.
     *
     * Returns true on success, or a Struct\Message on failure. SHOULD never
     * fail.
     *
     * @param string $reservationId
     * @return mixed
     */
    public function confirm($reservationId)
    {
        throw new \RuntimeException("@TODO: Implement");
    }
}
