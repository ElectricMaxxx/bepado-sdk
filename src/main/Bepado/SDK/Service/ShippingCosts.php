<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * @version $Revision$
 */

namespace Bepado\SDK\Service;

use Bepado\SDK\Gateway;
use Bepado\SDK\ShippingCostCalculator;

/**
 * Service to maintain transactions
 *
 * @version $Revision$
 */
class ShippingCosts
{
    /**
     * Shipping costs gateway
     *
     * @var Gateway\ShippingCosts
     */
    protected $shippingCosts;

    /**
     * Shipping cost calculator
     *
     * @var ShippingCostCalculator
     */
    protected $calculator;

    /**
     * COnstruct from gateway
     *
     * @param Gateway\ShippingCosts $shippingCosts
     * @param ShippingCostCalculator $calculator
     * @return void
     */
    public function __construct(
        Gateway\ShippingCosts $shippingCosts,
        ShippingCostCalculator $calculator
    ) {
        $this->shippingCosts = $shippingCosts;
        $this->calculator = $calculator;
    }

    /**
     * Get last revision
     *
     * @return string
     */
    public function lastRevision()
    {
        return $this->shippingCosts->getLastShippingCostsRevision();
    }

    /**
     * Replicate changes
     *
     * @param array $changes
     * @return void
     */
    public function replicate(array $changes)
    {
        foreach ($changes as $change) {
            $this->shippingCosts->storeShippingCosts(
                $change['shop'],
                $change['revision'],
                $change['shippingCosts']
            );
        }
    }
}
