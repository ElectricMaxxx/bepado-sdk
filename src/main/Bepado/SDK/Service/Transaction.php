<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK\Service;

use Bepado\SDK\ProductFromShop;
use Bepado\SDK\Gateway;
use Bepado\SDK\Logger;
use Bepado\SDK\Struct;
use Bepado\SDK\Struct\VerificatorDispatcher;
use Bepado\SDK\ShippingCostCalculator;

/**
 * Service to maintain transactions
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
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
     * Reservation gateway
     *
     * @var Gateway\ReservationGateway
     */
    protected $reservations;

    /**
     * Logger
     *
     * @var Logger
     */
    protected $logger;

    /**
     * ShopConfiguration
     *
     * @var ShopConfiguration
     */
    protected $shopConfiguration;

    /**
     * Shipping cost service
     *
     * @var ShippingCosts
     */
    protected $shippingCostsService;

    /**
     * @var \Bepado\SDK\Struct\VerificatorDispatcher
     */
    protected $verificator;

    /**
     * COnstruct from gateway
     *
     * @param ProductFromShop $fromShop
     * @param Gateway\ReservationGateway $reservations
     * @param Logger $logger
     * @return void
     */
    public function __construct(
        ProductFromShop $fromShop,
        Gateway\ReservationGateway $reservations,
        Logger $logger,
        Gateway\ShopConfiguration $shopConfiguration,
        ShippingCosts $shippingCostsService,
        VerificatorDispatcher $verificator
    ) {
        $this->fromShop = $fromShop;
        $this->reservations = $reservations;
        $this->logger = $logger;
        $this->shopConfiguration = $shopConfiguration;
        $this->shippingCostsService = $shippingCostsService;
        $this->verificator = $verificator;
    }

    /**
     * Check products that will be part of an order in shop
     *
     * Verifies, if all products in the list still have the same price
     * and availability as on the remote shop.
     *
     * Returns true on success, or an array of Struct\Change with updates for
     * the requested orders.
     *
     * @param Struct\ProductList $products
     * @return mixed
     */
    public function checkProducts(Struct\ProductList $products, $buyerShopId)
    {
        $this->verificator->verify($products);

        if (count($products->products) === 0) {
            throw new \InvalidArgumentException(
                "ProductList is not allowed to be empty in remote Transaction#checkProducts()"
            );
        }

        $currentProducts = $this->getCurrentProducts($products);

        $myShopId = $this->shopConfiguration->getShopId();
        $buyerShopConfig = $this->shopConfiguration->getShopConfiguration($buyerShopId);

        $changes = array();
        foreach ($products->products as $product) {
            if (!isset($currentProducts[$product->sourceId])) {
                // Product does not exist any more
                $changes[] = new Struct\Change\InterShop\Delete(
                    array(
                        'sourceId' => $product->sourceId,
                    )
                );

                continue;
            }

            $current = $currentProducts[$product->sourceId];
            $current->shopId = $myShopId;

            if ($this->purchasePriceHasChanged($current, $product, $buyerShopConfig->priceGroupMargin)) {

                $currentNotAvailable = clone $current;
                $currentNotAvailable->availability = 0;
                $currentNotAvailable->purchasePrice = $this->discountedPurchasePrice(
                    $current,
                    $buyerShopConfig->priceGroupMargin
                );

                $changes[] = new Struct\Change\InterShop\Update(
                    array(
                        'sourceId' => $product->sourceId,
                        'product' => $currentNotAvailable,
                        'oldProduct' => $product,
                    )
                );

            } elseif ($this->priceHasChanged($current, $product) ||
                $this->availabilityHasChanged($current, $product)) {

                $current->purchasePrice = $this->discountedPurchasePrice(
                    $current,
                    $buyerShopConfig->priceGroupMargin
                );

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

        return $changes ?: true;
    }

    /**
     * Get current products from the database indexed by source-id.
     *
     * @return array<string, \Bepado\SDK\Struct\Product>
     */
    private function getCurrentProducts($products)
    {
        $currentProducts = $this->fromShop->getProducts(
            array_map(
                function ($product) {
                    return $product->sourceId;
                },
                $products->products
            )
        );

        $indexedProducts = array();

        foreach ($currentProducts as $product) {
            $indexedProducts[$product->sourceId] = $product;
        }

        return $indexedProducts;
    }

    private function discountedPurchasePrice($current, $priceGroupMargin)
    {
        $discount = ($current->purchasePrice * $priceGroupMargin) / 100;
        return $current->purchasePrice - $discount;
    }

    private function purchasePriceHasChanged($current, $product, $priceGroupMargin)
    {
        return !$this->floatsEqual(
            $this->discountedPurchasePrice($current, $priceGroupMargin),
            $product->purchasePrice
        );
    }

    private function floatsEqual($a, $b)
    {
        return abs($a - $b) < 0.000001;
    }

    private function priceHasChanged($current, $product)
    {
        return ($current->fixedPrice && ! $this->floatsEqual($current->price, $product->price));
    }

    private function availabilityHasChanged($current, $product)
    {
        return $current->availability <= 0;
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
     * @param Struct\Order $order
     * @return mixed
     */
    public function reserveProducts(Struct\Order $order)
    {
        $this->verificator->verify($order);

        $products = array();
        foreach ($order->products as $orderItem) {
            $products[] = $orderItem->product;
        }

        $verify = $this->checkProducts(
            new Struct\ProductList(
                array(
                    'products' => $products
                )
            ),
            $order->orderShop
        );

        $myShippingCosts = $this->shippingCostsService->calculateShippingCosts(
            $order,
            Gateway\ShippingCosts::SHIPPING_COSTS_INTERSHOP
        );

        if (!$myShippingCosts->isShippable) {
            return new Struct\Message(array(
                'message' => 'Products cannot be shipped to %country.',
                'values' => array(
                    'country' => $order->deliveryAddress->country
                )
            ));
        }

        if (!$this->floatsEqual($order->shipping->shippingCosts, $myShippingCosts->shippingCosts) ||
            !$this->floatsEqual($order->shipping->grossShippingCosts, $myShippingCosts->grossShippingCosts)) {

            return new Struct\Message(
                array(
                    'message' => "Shipping costs have changed from %oldValue to %newValue.",
                    'values' => array(
                        'oldValue' => round($order->grossShippingCosts, 2),
                        'newValue' => round($myShippingCosts->grossShippingCosts, 2),
                    ),
                )
            );
        }

        if ($verify !== true) {
            return $verify;
        }

        try {
            $reservationId = $this->reservations->createReservation($order);
            $this->fromShop->reserve($order);
        } catch (\Exception $e) {
            return new Struct\Error(
                array(
                    'message' => $e->getMessage(),
                    'debugText' => (string) $e,
                )
            );
        }
        return $reservationId;
    }

    /**
     * Buy order associated with reservation in the remote shop.
     *
     * Returns true on success, or a Struct\Message on failure. SHOULD never
     * fail.
     *
     * @param string $reservationId
     * @param string $orderId
     * @return mixed
     */
    public function buy($reservationId, $orderId)
    {
        try {
            $order = $this->reservations->getOrder($reservationId);
            $order->localOrderId = $orderId;
            $order->providerOrderId = $this->fromShop->buy($order);
            $order->reservationId = $reservationId;
            $this->reservations->setBought($reservationId, $order);
            return $this->logger->log($order);
        } catch (\Exception $e) {
            return new Struct\Error(
                array(
                    'message' => $e->getMessage(),
                    'debugText' => (string) $e,
                )
            );
        }
    }

    /**
     * Confirm a reservation in the remote shop.
     *
     * Returns true on success, or a Struct\Message on failure. SHOULD never
     * fail.
     *
     * @param string $reservationId
     * @param string $remoteLogTransactionId
     * @return mixed
     */
    public function confirm($reservationId, $remoteLogTransactionId)
    {
        try {
            $order = $this->reservations->getOrder($reservationId);
            $this->reservations->setConfirmed($reservationId);
            $this->logger->confirm($remoteLogTransactionId);
        } catch (\Exception $e) {
            return new Struct\Error(
                array(
                    'message' => $e->getMessage(),
                    'debugText' => (string) $e,
                )
            );
        }
        return true;
    }
}
