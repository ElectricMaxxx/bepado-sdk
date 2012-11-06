<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK;

/**
 * Shop factory
 *
 * Constructs gateways to interact with other shops
 *
 * @version $Revision$
 */
class ShopFactory
{
    /**
     * Get shop gateway for shop
     *
     * @param string $shopId
     * @return ShopGateway
     */
    public function getShopGateway($shopId)
    {
        throw new \RuntimeException('@TODO: Implement');
    }
}
