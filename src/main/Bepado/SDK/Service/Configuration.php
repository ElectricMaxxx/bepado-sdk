<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * @version $Revision$
 */

namespace Bepado\SDK\Service;

use Bepado\SDK\Gateway;
use Bepado\SDK\HttpClient;
use Bepado\SDK\Struct;

/**
 * Service to store configuration updates
 *
 * @version $Revision$
 */
class Configuration
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
     */
    public function __construct(Gateway\ShopConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Store shop configuration updates
     *
     * @param array $configurations
     * @param array $features
     *
     * @return void
     */
    public function update(array $configurations, array $features = null)
    {
        foreach ($configurations as $configuration) {
            $this->configuration->setShopConfiguration(
                $configuration['shopId'],
                new Struct\ShopConfiguration(
                    array(
                        'serviceEndpoint'  => $configuration['serviceEndpoint'],
                        'shippingCost'     => $configuration['shippingCost'],
                        'displayName'      => $configuration['shopDisplayName'],
                        'url'              => $configuration['shopUrl'],
                        'key'              => $configuration['key'],
                        'priceGroupMargin' => $configuration['priceGroupMargin'],
                    )
                )
            );
        }

        if (is_array($features)) {
            $this->configuration->setEnabledFeatures($features);
        }
    }
}
