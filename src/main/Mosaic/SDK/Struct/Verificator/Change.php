<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Struct\Verificator;

use Mosaic\SDK\Struct\Verificator,
    Mosaic\SDK\Struct\VerificatorDispatcher,
    Mosaic\SDK\Struct;

/**
 * Visitor verifying integrity of struct classes
 *
 * @version $Revision$
 */
class Change extends Verificator
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
        if ($struct->sourceId === null) {
            throw new \RuntimeException('Property $sourceId must be set.');
        }

        if ($struct->revision === null) {
            throw new \RuntimeException('Property $revision must be set.');
        }
    }
}
