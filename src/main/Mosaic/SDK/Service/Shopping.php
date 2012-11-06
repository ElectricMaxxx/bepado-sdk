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

    public function __construct(ShopFactory $shopFactory)
    {
        $this->shopFactory = $shopFactory;
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
        $results = array();
        foreach ($this->getShopIds($order) as $shopId) {
            $shopGateway = $this->shopFactory->getShopGateway($shopId);
            $shopProducts = $this->getShopProducts($order, $shopId);

            $results['shopId'] = $shopGateway->checkProducts($shopProducts);
        }

        return $this->mergeMessages($results);
    }

    protected function getShopIds(Struct\Order $order)
    {
         return array_unique(array_map(
            function (Struct\OrderItem $orderItem) {
                return $orderItem->product->shopId;
            },
            $order->products
        ));
    }

    protected function getShopProducts(Struct\Order $order, $shopId)
    {
        return array_filter(
            $order->products,
            function (Struct\Product $orderItem) use ($shopId) {
                return $orderItem->product->shopId === $shopId;
            }
        );
    }

    protected function mergeMessages(array $messages)
    {
        return array_reduce(
            $results,
            function($prev, $next) {
                if ($next === true ) {
                    return $prev;
                } elseif (is_array($prev)) {
                    return array_merge($prev, $next);
                } else {
                    return $next;
                }
            },
            true
        );
    }
}
