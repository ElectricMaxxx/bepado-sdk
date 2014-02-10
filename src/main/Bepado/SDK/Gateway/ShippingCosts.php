<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * @version $Revision$
 */

namespace Bepado\SDK\Gateway;

use Bepado\SDK\Struct;

/**
 * Gateaway interface to maintain shipping costs
 *
 * @version $Revision$
 * @api
 */
interface ShippingCosts
{
    /**
     * Get last revision
     *
     * @return string
     */
    public function getLastShippingCostsRevision();

    /**
     * Store shop shipping costs
     *
     * @param string $shop
     * @param string $revision
     * @param array $shippingCosts
     * @return void
     */
    public function storeShippingCosts($shop, $revision, $shippingCosts);

    /**
     * Get shop shipping costs
     *
     * @param string $shop
     * @return array
     */
    public function getShippingCosts($shop);
}
