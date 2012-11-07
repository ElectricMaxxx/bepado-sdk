<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Struct\Verificator;

use Mosaic\SDK\Struct\Verificator;

/**
 * Visitor verifying integrity of struct classes
 *
 * @version $Revision$
 */
class Product extends Verificator
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
        foreach (array(
                'shopId',
                'sourceId',
                'price',
                'currency',
                'availability',
            ) as $property) {
            if ($struct->$property === null) {
                throw new \RuntimeException("Property $property MUST be set in product.");
            }
        }
    }
}
