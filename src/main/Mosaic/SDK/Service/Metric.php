<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Service;

use Mosaic\SDK\Gateway;
use Mosaic\SDK\ProductFromShop;
use Mosaic\SDK\RevisionProvider;
use Mosaic\SDK\ProductHasher;
use Mosaic\SDK\Gateway\ChangeGateway;
use Mosaic\SDK\Gateway\ProductGateway;

/**
 * Service to receive current shop metrics
 *
 * @version $Revision$
 */
class Metric
{
    /**
     * Gateway to changes feed
     *
     * @var \Mosaic\SDK\Gateway\ChangeGateway
     */
    protected $changes;

    /**
     * Construct from gateway
     *
     * @param \Mosaic\SDK\Gateway\ChangeGateway $changes
     */
    public function __construct(
        ChangeGateway $changes
    ) {
        $this->changes = $changes;
    }

    /**
     * Get current shop metrics
     *
     * @return array
     */
    public function getMetrics()
    {
        return array();
    }
}
