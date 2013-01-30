<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Service;

use Mosaic\SDK\Gateway\ChangeGateway;
use Mosaic\Common\Struct;

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
     * Export current change state to Mosaic
     *
     * @param string $revision
     * @param int $productCount
     * @return \Mosaic\Common\Struct\Metric[]
     */
    public function fromShop($revision, $productCount)
    {
        return array(
            new Struct\Metric\Count(
                array(
                    'name' => 'sdk.changes_backlog',
                    'count' => $this->changes->getUnprocessedChangesCount($revision, $productCount),
                )
            ),
        );
    }
}
