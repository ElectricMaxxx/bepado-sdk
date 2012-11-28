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
class Message extends Verificator
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
        if (!is_string($struct->message)) {
            throw new \RuntimeException('$message MUST be a string.');
        }

        if (!is_array($struct->values)) {
            throw new \RuntimeException('$values MUST be an array.');
        }
    }
}
