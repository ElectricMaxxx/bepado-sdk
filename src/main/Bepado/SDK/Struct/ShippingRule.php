<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK\Struct;

use Bepado\SDK\Exception\InvalidArgumentException;
use Bepado\SDK\Struct;

/**
 * Definition of a Product shipping rule.
 */
class ShippingRule extends Struct
{
    /**
     * @var string
     */
    public $country;

    /**
     * @var string
     */
    public $region;

    /**
     * @var string
     */
    public $zipRange;

    /**
     * @var int
     */
    public $deliveryWorkDays;

    /**
     * @var string
     */
    public $service;

    /**
     * @var float
     */
    public $price;

    /**
     * @var string
     */
    public $currency;

    static public function fromString($shipping)
    {
        if (strpos($shipping, ',') !== false) {
            throw new InvalidArgumentException('Invalid shipping rule includes comma.');
        }

        $parts = explode(':', trim($shipping));

        if (count($parts) != 4) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid shipping rule passed "%s", required to be seperated by 3 double-colons, ex: DE:::3.99 EUR',
                    $shipping
                )
            );
        }

        list ($country, $region, $service, $price) = $parts;

        list ($maxDeliveryDays, $service) = self::parseMaxDelivery($service);
        list ($price, $currency) = self::parsePrice($price);

        return new ShippingRule(array(
            'country'          => $country,
            'service'          => $service,
            'region'           => $region,
            'price'            => $price,
            'currency'         => $currency,
            'deliveryWorkDays' => $maxDeliveryDays,
        ));
    }

    private function parsePrice($price)
    {
        if (!$price) {
            return array($price, null);
        }

        list ($price, $currency) = explode(' ', $price);

        $formatter = new \NumberFormatter('en_US', \NumberFormatter::DECIMAL);
        $price = (float)$formatter->parse($price);

        return array($price, $currency);
    }

    private function parseMaxDelivery($service)
    {
        $maxDeliveryDays = null;

        if (preg_match('((P([0-9]+)D+))', $service, $match)) {
            $maxDeliveryDays = (int)$match[2];
            $service = trim(str_replace($match[1], '', $service));
        } else if (preg_match('((PT([0-9]+)H+))', $service, $match)) {
            $maxDeliveryDays = (int)ceil($match[2] / 24);
            $service = trim(str_replace($match[1], '', $service));
        }

        return array($maxDeliveryDays, $service);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $price = sprintf('%s %s', number_format(round($this->price, 2), 2, '.', ''), $this->currency);

        $service = $this->service;

        if ($this->deliveryWorkDays) {
            $service = $this->service . ' P' . $this->deliveryWorkDays . 'T';
        }

        return implode(':', array($this->country, $this->region, $this->service, $price));
    }
}
