<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\ShopFactory;

use Mosaic\SDK\ShopFactory;
use Mosaic\SDK\ProductToShop;
use Mosaic\SDK\ProductFromShop;
use Mosaic\SDK\ShopGateway;
use Mosaic\SDK\Gateway;
use Mosaic\SDK\Logger;
use Mosaic\SDK\SDK;

/**
 * Shop factory
 *
 * Constructs gateways to interact with other shops
 *
 * @private
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

    /**
     * Logger
     *
     * @var Logegr
     */
    protected $logger;

    /**
     * Shop gateways
     *
     * @var ShopGateway[]
     */
    protected $shopGateways = array();

    public function __construct(
        ProductToShop $toShop,
        ProductFromShop $fromShop,
        Logger $logger
    ) {
        $this->toShop = $toShop;
        $this->fromShop = $fromShop;
        $this->logger = $logger;
    }

    /**
     * Get shop gateway for shop
     *
     * @param string $shopId
     * @return ShopGateway
     */
    public function getShopGateway($shopId)
    {
        if (!isset($this->shopGateways[$shopId])) {
            $sdk = new SDK(
                new Gateway\InMemory(),
                $this->toShop,
                $this->fromShop
            );

            // Inject custom logger
            $loggerProperty = new \ReflectionProperty(get_class($sdk), 'logger');
            $loggerProperty->setAccessible(true);
            $loggerProperty->setValue($sdk, $this->logger);

            $this->shopGateways[$shopId] = new ShopGateway\DirectAccess($sdk);
        }

        return $this->shopGateways[$shopId];
    }
}
