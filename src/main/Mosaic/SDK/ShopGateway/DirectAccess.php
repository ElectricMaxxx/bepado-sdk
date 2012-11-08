<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\ShopGateway;

use Mosaic\SDK\ShopGateway;
use Mosaic\SDK\SDK;

/**
 * Shop gateway base class
 *
 * Gateway to interact with other shops
 *
 * @TODO: We might want to integrate marshalling and unmarshalling here. In
 * this case we'd call the handle() method from the SDK with the marshalled
 * request and unmarshal the response before returning it. Would be a better
 * simulation of the real execution.
 *
 * @version $Revision$
 */
class DirectAccess extends ShopGateway
{
    /**
     * SDK
     *
     * @var SDK
     */
    protected $sdk;

    public function __construct(SDK $sdk)
    {
        $this->sdk = $sdk;
    }

    /**
     * Reserve order in remote shop
     *
     * Products SHOULD be reserved and not be sold out while bing reserved.
     * Reservation may be cancelled after sufficient time has passed.
     *
     * Returns true on success, or an array of Struct\Change with updates for
     * the requested products.
     *
     * @param Struct\Order
     * @return mixed
     */
    public function checkProducts(Struct\Order $order)
    {
       return $this->sdk->getServiceRegistry()->dispatch(
            new Struct\RpcCall(array(
                'service' => 'transaction',
                'command' => 'checkProducts',
                'arguments' => array($order),
            ))
        );
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
       return $this->sdk->getServiceRegistry()->dispatch(
            new Struct\RpcCall(array(
                'service' => 'transaction',
                'command' => 'reserveProducts',
                'arguments' => array($order),
            ))
        );
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
       return $this->sdk->getServiceRegistry()->dispatch(
            new Struct\RpcCall(array(
                'service' => 'transaction',
                'command' => 'buy',
                'arguments' => array($reservationId),
            ))
        );
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
       return $this->sdk->getServiceRegistry()->dispatch(
            new Struct\RpcCall(array(
                'service' => 'transaction',
                'command' => 'confirm',
                'arguments' => array($reservationId),
            ))
        );
    }
}
