<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * @version $Revision$
 */

namespace Bepado\SDK\Service;

use Bepado\SDK\ProductFromShop;
use Bepado\SDK\Gateway;
use Bepado\SDK\Logger;
use Bepado\SDK\Struct;
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
     * @var Gateway\ShopConfiguration
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
     * @param Gateway\ShopConfiguration $shippingCosts
     * @param ShippingCostCalculator $calculator
     * @return void
     */
    public function __construct(
        Gateway\ShopConfiguration $shippingCosts,
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
        return null;
    }
}
