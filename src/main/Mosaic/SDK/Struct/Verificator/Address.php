<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Struct\Verificator;

use Mosaic\SDK\Struct\Verificator;
use Mosaic\SDK\Struct\VerificatorDispatcher;
use Mosaic\SDK\Struct;

/**
 * Visitor verifying integrity of struct classes
 *
 * @version $Revision$
 */
class Address extends Verificator
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
        foreach (array('name', 'line1', 'zip', 'city', 'country') as $required) {
            if (!is_string($struct->$required)) {
                throw new \RuntimeException($required . ' MUST be a string.');
            }
        }
    }
}
