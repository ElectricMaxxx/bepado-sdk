<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\ShopFactory;

use Mosaic\SDK\ShopFactory;
use Mosaic\SDK\ShopGateway;
use Mosaic\SDK\Gateway;
use Mosaic\SDK\SDK;

/**
 * Shop factory
 *
 * Constructs gateways to interact with other shops
 *
 * @version $Revision$
 */
class DirectAccess extends ShopFactory
{
    /**
     * Product toShop
     *
     * @var ProductToShop
     */
    protected $toShop;

    /**
     * Product fromShop
     *
     * @var ProductFromShop
     */
    protected $fromShop;

    public function __construct(
        ProductToShop $toShop,
        ProductFromShop $fromShop
    ) {
        $this->toShop = $toShop;
        $this->fromShop = $fromShop;
    }

    /**
     * Get shop gateway for shop
     *
     * @param string $shopId
     * @return ShopGateway
     */
    public function getShopGateway($shopId)
    {
        return ShopGateway\DirectAccess(
            new SDK(
                new Gateway\InMemory(),
                $this->toShop,
                $this->fromShop
            )
        );
    }
}