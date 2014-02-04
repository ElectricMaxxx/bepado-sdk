<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * @version $Revision$
 */

namespace Bepado\SDK;

/**
 * Shipping cost calculator
 *
 * @version $Revision$
 */
class ShippingCostCalculator
{
    /**
     * Shipping cost calculator
     *
     * @var ShippingCostCalculator
     */
    protected $calculator;

    public function __construct(
        Gateway\ShopConfiguration $configuration
    ) {
        $this->configuration = $configuration;
    }

    /**
     * Get shipping costs for order
     *
     * @param Struct\Order $order
     * @return Struct\Order
     */
    public function calculateShippingCosts(Struct\Order $order)
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
     * @param Struct\Order $order
     * @return ShippingCostCalculator\Rule[]
     */
    protected function getShippingCostRules(Struct\Order $order)
    {
        $shopIds = array_unique(
            array_map(
                function (Struct\OrderItem $orderItem) {
                    return $orderItem->product->shopId;
                },
                $order->products
            )
        );

        if (count($shopIds) > 1) {
            throw new \InvalidArgumentException(
                "ShippingCostCalculator can only calculate shipping costs for " .
                "products belonging to exactly one remote shop."
            );
        }

        $shopId = reset($shopIds);

        $shopConfiguration = $this->configuration->getShopConfiguration($shopId);

        // @TODO: This should be replaced by some factory crafting the shipping
        // cost rules from some DSL. For now we only support fixed price
        // shipping cost rules.
        return array(
            new ShippingCostCalculator\Rule\FixedPrice($shopConfiguration->shippingCost)
        );
    }

    /**
     * Get maximum VAT of all products
     *
     * This seems to be a safe assumption to apply the maximum VAT of all
     * products to the shipping costs.
     *
     * @param Struct\Order $order
     * @return float
     */
    protected function getMaximumVat(Struct\Order $order)
    {
        return max(
            array_map(
                function (Struct\OrderItem $orderItem) {
                    return $orderItem->product->vat;
                },
                $order->products
            )
        );
    }
}
