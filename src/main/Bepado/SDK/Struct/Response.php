<?php
/**
 * This file is part of the Bepado Common Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK\Struct;

use Bepado\SDK\Struct;

/**
 * Struct class representing a message
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 * @api
 */
class Response extends Struct
{
    /**
     * Result of the response
     *
     * Can be about anything
     *
     * @var mixed
     */
    public $result;

    /**
     * Metrics
     *
     * Array of metrics, transmitted with the response. Will be logged by the
     * updater.
     *
     * @var array
     */
    public $metrics = array();

    /**
     * Version string of the answering component
     *
     * @var string
     */
    public $version;
}
