<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Service;

use Mosaic\SDK\Gateway;
use Mosaic\SDK\Struct;
use Mosaic\SDK\ShopFactory;
use Mosaic\SDK\ChangeVisitor;

/**
 * Shopping service
 *
 * @version $Revision$
 */
class Shopping
{
    /**
     * Shop registry
     *
     * @var ShopFactory
     */
    protected $shopFactory;

    /**
     * CHange visitor
     *
     * Visits arrays of changes into corresponding messages
     *
     * @var ChangeVisitor
     */
    protected $changeVisitor;

    public function __construct(ShopFactory $shopFactory, ChangeVisitor $changeVisitor)
    {
        $this->shopFactory = $shopFactory;
        $this->changeVisitor = $changeVisitor;
    }

    /**
     * Check products still are in the state they are stored locally
     *
     * This method will verify with the remote shops that products are still in
     * the expected state. If the state of products changed this method will
     * return a Struct\Message, which should be ACK'ed by the user. Otherwise
     * this method will just return true.
     *
     * If data updated are detected, the local product database will be updated 
     * accordingly.
     *
     * This method is a convenience method to check the state of a set of
     * remote products. The state will be checked again during
     * reserveProducts().
     *
     * @param Struct\Order $order
     * @return void
     */
    public function checkProducts(Struct\Order $order)
    {
        $responses = $this->callShopsForOrder('checkProducts', $order);

        $result = array();
        foreach ($responses as $shop => $changes) {
            if ($changes !== true) {
                $result = array_merge(
                    $result,
                    $this->changeVisitor->visit($changes)
                );
            }
        }

        return $result ?: true;
    }

    /**
     * Reserve products
     *
     * This method will reserve the given products in the remote shops.
     *
     * If the product data change in a relevant way, this method will not
     * reserve the products, but instead return a Struct\Message, which should
     * be ACK'ed by the user. Afterwards another reservation may be issued.
     *
     * If The reservation of the product set succeeded a hash of reservation
     * IDs for all involved shops will be returned. This hash must be stored in
     * the shop for all further transactions. The session is probably the best
     * location for this.
     *
     * If data updated are detected, the local product database will be updated
     * accordingly.
     *
     * @TODO: How do we want to handle the case that some shop reserve the
     * order as requested, and others complain. Just ignore because it is bound
     * to happen really seldom?
     *
     * @param Struct\Order $order
     * @return Struct\Reservation
     */
    public function reserveProducts(Struct\Order $order)
    {
        $responses = $this->callShopsForOrder('reserveProducts', $order);

        $reservation = new Struct\Reservation();
        foreach ($responses as $shop => $response) {
            if (!is_string($response)) {
                $reservation->messages[$shop] = $this->changeVisitor->visit($response);
            } else {
                $reservation->reservationIDs[$shop] = $response;
            }
        }

        return $reservation;
    }

    /**
     * Checkout product sets related to the given reservation IDs
     *
     * This process is the final "buy" transaction. It should be part of the
     * checkout process and be handled synchronously.
     *
     * This method will just return true, if the transaction worked as
     * expected. If it failed, or partially failed, a corresponding
     * Struct\Message will be returned.
     *
     * @param string[] $products
     * @return mixed
     */
    public function checkout(array $reservationIDs)
    {
        // @TODO: 1) Buy
        // @TODO: 2) Confirm
        return true;
    }

    protected function callShopsForOrder($method, Struct\Order $order)
    {
        $results = array();
        foreach ($this->getShopIds($order) as $shopId) {
            $shopGateway = $this->shopFactory->getShopGateway($shopId);

            $shopOrder = clone $order;
            $order->products = $this->getShopProducts($order, $shopId);
            $results[$shopId] = $shopGateway->$method($order);
        }

        return $results;
    }

    protected function getShopIds(Struct\Order $order)
    {
        return array_unique(
            array_map(
                function (Struct\OrderItem $orderItem) {
                    return $orderItem->product->shopId;
                },
                $order->products
            )
        );
    }

    protected function getShopProducts(Struct\Order $order, $shopId)
    {
        return array_filter(
            $order->products,
            function (Struct\OrderItem $orderItem) use ($shopId) {
                return $orderItem->product->shopId === $shopId;
            }
        );
    }
}
