<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * @version $Revision$
 */

namespace Bepado\SDK\ShippingCostCalculator;

use Bepado\SDK\Gateway\ShippingCosts;
use Bepado\SDK\ShippingCostCalculator;
use Bepado\SDK\Struct\Order;
use Bepado\SDK\Struct\OrderItem;

/**
 * Calculate shipping costs based on rules from the gateway.
 */
class RuleCalculator implements ShippingCostCalculator
{
    /**
     * Shipping costs gateway
     *
     * @var Gateway\ShippingCosts
     */
    protected $shippingCosts;

    public function __construct(ShippingCosts $shippingCosts)
    {
        $this->shippingCosts = $shippingCosts;
    }

    /**
     * Get shipping costs for order
     *
     * @param \Bepado\SDK\Struct\Order $order
     * @return \Bepado\SDK\Struct\Order
     */
    public function calculateShippingCosts(Order $order)
    {
        $shippingCostRules = $this->getShippingCostRules($order);
        $maximumVat = $this->getMaximumVat($order);
        $netShippingCosts = 0;

        foreach ($shippingCostRules as $shippingCostRule) {
            if ($shippingCostRule->isApplicable($order)) {
                $netShippingCosts += $shippingCostRule->getShippingCosts($order);

                if ($shippingCostRule->shouldStopProcessing($order)) {
                    break;
                }
            }
        }

        $order->shippingCosts = $netShippingCosts;
        $order->grossShippingCosts = $netShippingCosts * (1 + $maximumVat);

        return $order;
    }

    /**
     * Get shipping cost rules for current order
     *
     * @param \Bepado\SDK\Struct\Order $order
     * @return Rule[]
     */
    protected function getShippingCostRules(Order $order)
    {
        if (empty($order->providerShop) || empty($order->orderShop)) {
            throw new \InvalidArgumentException(
                "Order#providerShop and Order#orderShop must be non-empty ".
                "to calculate the shipping costs."
            );
        }

        foreach ($order->products as $orderItem) {
            if ($orderItem->product->shopId != $order->providerShop) {
                throw new \InvalidArgumentException(
                    "ShippingCostCalculator can only calculate shipping costs for " .
                    "products belonging to exactly one remote shop."
                );
            }
        }

        return $this->shippingCosts->getShippingCosts($order->providerShop, $order->orderShop);
    }

    /**
     * Get maximum VAT of all products
     *
     * This seems to be a safe assumption to apply the maximum VAT of all
     * products to the shipping costs.
     *
     * @param \Bepado\SDK\Struct\Order $order
     * @return float
     */
    protected function getMaximumVat(Order $order)
    {
        return max(
            array_map(
                function (OrderItem $orderItem) {
                    return $orderItem->product->vat;
                },
                $order->products
            )
        );
    }
}
