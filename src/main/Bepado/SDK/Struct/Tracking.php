<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK\Struct;

use Bepado\SDK\Struct;

class Tracking extends Struct
{
    const VENDOR_DHL = 'dhl';
    const VENDOR_HERMES = 'hermes';
    const VENDOR_UPS = 'ups';
    const VENDOR_GLS = 'gls';
    const VENDOR_DPD = 'dpd';
    const VENDOR_FEDEX = 'fedex';

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $vendor;

    /**
     * @var string
     */
    public $url;
}
