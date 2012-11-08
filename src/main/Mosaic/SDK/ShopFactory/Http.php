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

/**
 * Shop factory
 *
 * Constructs gateways to interact with other shops
 *
 * @version $Revision$
 */
class Http extends ShopFactory
{
    /**
     * Gateway to shop configuration
     *
     * @var Gateway\ShopConfiguration
     */
    protected $configuration;

    /**
     * Construct from gateway
     *
     * @param Gateway\ShopConfiguration $gateway
     * @return void
     */
    public function __construct(
        Gateway\ShopConfiguration $configuration
    ) {
        $this->configuration = $configuration;
    }

    /**
     * Get shop gateway for shop
     *
     * @param string $shopId
     * @return ShopGateway
     */
    public function getShopGateway($shopId)
    {
        $configuration = $this->configuration->getShopConfiguration($shopId);
        return new ShopGateway\Http($configuration->serviceEndpoint);
    }
}
