<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Service;

use Mosaic\SDK\ProductFromShop;
use Mosaic\SDK\Struct;

/**
 * Service to maintain transactions
 *
 * @version $Revision$
 */
class Transaction
{
    /**
     * Implementation of the interface to receive orders from the shop
     *
     * @var ProductFromShop
     */
    protected $fromShop;

    /**
     * COnstruct from gateway
     *
     * @param ProductFromShop $fromShop
     * @return void
     */
    public function __construct(
        ProductFromShop $fromShop
    ) {
        $this->fromShop = $fromShop;
    }

    /**
     * Check order in shop
     *
     * Verifies, if all orders in the given order still have the same price
     * and availability.
     *
     * Returns true on success, or an array of Struct\Change with updates for
     * the requested orders.
     *
     * @param Struct\OrderItem[] $orders
     * @return mixed
     */
    public function checkProducts(array $orders)
    {
        $currentProducts = $this->fromShop->getProducts(
            array_map(
                function ($orderItem) {
                    return $orderItem->product->sourceId;
                },
                $orders
            )
        );

        $changes = array();
        foreach ($orders as $orderItem) {
            $product = $orderItem->product;
            foreach ($currentProducts as $current) {
                if ($current->sourceId === $product->sourceId) {
                    if (($current->price !== $product->price) ||
                        ($current->availability < $product->availability)) {

                        // Price or availability changed
                        $changes[] = new Struct\Change\InterShop\Update(
                            array(
                                'sourceId' => $product->sourceId,
                                'product' => $current,
                                'oldProduct' => $product,
                            )
                        );
                    }
                }

                continue 2;
            }

            // Product does not exist any more
            $changes[] = new Struct\Change\InterShop\Delete(
                array(
                    'sourceId' => $product->sourceId,
                )
            );
        }

        return $changes ?: true;
    }

    /**
     * Reserve order in shop
     *
     * ProductGateway SHOULD be reserved and not be sold out while bing reserved.
     * Reservation may be cancelled after sufficient time has passed.
     *
     * Returns a reservationId on success, or an array of Struct\Change with
     * updates for the requested orders.
     *
     * @param Struct\OrderItem[] $orders
     * @return mixed
     */
    public function reserveProducts(array $orders)
    {
        $verify = $this->checkProducts($orders);
        if ($verify !== true) {
            return $verify;
        }

        // @TODO: Actually reserve
        return 'foo';
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
        // @TODO: Buy orders
        return true;
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
        // @TODO: Confirm buy and log transaction
        return true;
    }
}
