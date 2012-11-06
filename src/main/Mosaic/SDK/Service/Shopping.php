<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Service;

use Mosaic\SDK\Gateway;
use Mosaic\SDK\Struct;

/**
 * Shopping service
 *
 * @version $Revision$
 */
class Shopping
{
    /**
     * Check products still are in the state they are stored locally
     *
     * This method will verify with the remote shops that products are still in
     * the expected state. If the state of products changed this method will
     * return a Struct\Message, which should be ACK'ed by the user. Otherwise
     * this method will just return true.
     *
     * If data updated are detected, the local product database will be updated 
     * accordingly.
     *
     * This method is a convenience method to check the state of a set of
     * remote products. The state will be checked again during
     * reserveProducts().
     *
     * @param Struct\Order $order
     * @return void
     */
    public function checkProducts(Struct\Order $order)
    {
        throw new \RuntimeException('@TODO: Implement');
    }
}
