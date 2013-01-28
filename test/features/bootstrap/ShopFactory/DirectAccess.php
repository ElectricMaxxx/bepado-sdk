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
     * Gateway
     *
     * @var Gateway
     */
    protected $gateway;

    /**
     * Logger
     *
     * @var Logger
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
        Gateway $gateway,
        Logger $logger
    ) {
        $this->toShop = $toShop;
        $this->fromShop = $fromShop;
        $this->gateway = $gateway;
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
            $this->gateway->setShopId($shopId);
            $sdk = new SDK(
                'apikey-' . $shopId,
                'http://example.com/endpoint-' . $shopId,
                $this->gateway,
                $this->toShop,
                $this->fromShop
            );

            // Inject custom logger
            $dependenciesProperty = new \ReflectionProperty($sdk, 'dependencies');
            $dependenciesProperty->setAccessible(true);
            $dependencies = $dependenciesProperty->getValue($sdk);

            $loggerProperty = new \ReflectionProperty($dependencies, 'logger');
            $loggerProperty->setAccessible(true);
            $loggerProperty->setValue($dependencies, $this->logger);

            $this->shopGateways[$shopId] = new ShopGateway\DirectAccess($sdk);
        }

        return $this->shopGateways[$shopId];
    }
}
