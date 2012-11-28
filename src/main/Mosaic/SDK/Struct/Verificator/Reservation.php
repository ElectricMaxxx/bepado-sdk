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

use Mosaic\SDK\Struct\Message;

/**
 * Visitor verifying integrity of struct classes
 *
 * @version $Revision$
 */
class Reservation extends Verificator
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
        if (!is_array($struct->messages)) {
            throw new \RuntimeException('$messages MUST be an array.');
        }
        foreach ($struct->messages as $message) {
            if (!$message instanceof Message) {
                throw new \RuntimeException('$message MUST be an instance of \\Mosaic\\SDK\\Struct\\Message.');
            }
            $dispatcher->verify($message);
        }

        if (!is_array($struct->reservationIDs)) {
            throw new \RuntimeException('$reservationIDs MUST be an array.');
        }
        foreach ($struct->reservationIDs as $reservationID) {
            if (!is_string($reservationID)) {
                throw new \RuntimeException('$reservationID MUST be a string.');
            }
        }
    }
}
