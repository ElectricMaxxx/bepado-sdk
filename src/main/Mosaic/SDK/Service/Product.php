<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Service;

use Mosaic\SDK\Gateway;

/**
 * Product service
 *
 * @version $Revision$
 */
class Product
{
    /**
     * Gateway to changes feed
     *
     * @var Gateway
     */
    protected $gateway;

    /**
     * COnstruct from gateway
     *
     * @param Gateway $gateway
     * @return void
     */
    public function __construct(Gateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Export current change state to Mosaic
     *
     * @param string $revision
     * @param int $productCount
     * @return Struct\Change[]
     */
    public function export($revision, $productCount)
    {
        return $this->gateway->getNextChanges($revision, $productCount);
    }
}
