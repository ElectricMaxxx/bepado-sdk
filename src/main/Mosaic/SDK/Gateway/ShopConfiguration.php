<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Gateway;

use Mosaic\SDK\Struct;

/**
 * Gateaway interface to maintain shop configurations
 *
 * @version $Revision$
 * @api
 */
interface ShopConfiguration
{
    /**
     * Update shop configuration
     *
     * @param string $shopId
     * @param Struct\ShopConfiguration $configuration
     * @return void
     */
    public function setShopConfiguration($shopId, Struct\ShopConfiguration $configuration);

    /**
     * Get configuration for the given shop
     *
     * @param string $shopId
     * @return Struct\ShopConfiguration
     */
    public function getShopConfiguration($shopId);

    /**
     * Set own shop ID
     *
     * @param string $shopId
     * @return void
     */
    public function setShopId($shopId);

    /**
     * Get own shop ID
     *
     * @return string
     */
    public function getShopId();
}
