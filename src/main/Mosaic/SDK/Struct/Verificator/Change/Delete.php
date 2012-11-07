<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Struct\Verificator\Change;

use Mosaic\SDK\Struct\Verificator\Change;
use Mosaic\SDK\Struct\Product;

/**
 * Visitor verifying integrity of struct classes
 *
 * @version $Revision$
 */
class Delete extends Change
{
    /**
     * Method to verify a structs integrity
     *
     * Throws a RuntimeException if the struct does not verify.
     *
     * @param VerificatorDispatcher $dispatcher
     * @param Struct $struct
     * @return void
     */
    public function verify(VerificatorDispatcher $dispatcher, Struct $struct)
    {
        parent::verify($dispatcher, $struct);

        if ($struct->shopId === null) {
            throw new \RuntimeException('Property $shopId must be set.');
        }
    }
}
