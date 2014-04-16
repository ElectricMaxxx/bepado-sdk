<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK\Service;

use Bepado\SDK\Gateway;
use Bepado\SDK\HttpClient;
use Bepado\SDK\Struct;

/**
 * Service to store configuration updates
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
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
     * @param \Bepado\SDK\Struct\Address $billing
     *
     * @return void
     */
    public function update(array $configurations, array $features, Struct\Address $billing)
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

        $this->configuration->setEnabledFeatures($features);
        $this->configuration->setBillingAddress($billing);
    }

    public function replicate(array $changes)
    {
        foreach ($changes as $change) {
            $config = $change['configuration'];
            $this->update(
                $config->shops,
                $config->features,
                $config->billingAddress
            );
        }
    }

    public function lastRevision()
    {
        return 0; // always replicate
    }
}
