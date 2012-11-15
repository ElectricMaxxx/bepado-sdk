<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Gateway;

/**
 * Gateaway interface to maintain revision data
 *
 * @version $Revision$
 * @api
 */
interface Revision
{
    /**
     * Get last processed import revision
     *
     * @return string
     */
    public function getLastRevision();

    /**
     * Store last processed import revision
     *
     * @param string $revision
     * @return void
     */
    public function storeLastRevision($revision);
}
