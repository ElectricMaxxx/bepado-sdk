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
        foreach ($struct->messages as $shopId => $messages) {
            if (!is_array($messages)) {
                throw new \RuntimeException('$messages MUST be an array.');
            }

            foreach ($messages as $message) {
                if (!$message instanceof Struct\Message) {
                    throw new \RuntimeException('$message MUST be an instance of \\Mosaic\\SDK\\Struct\\Message.');
                }
                $dispatcher->verify($message);
            }
        }

        if (!is_array($struct->orders)) {
            throw new \RuntimeException('$orders MUST be an array.');
        }
        foreach ($struct->orders as $order) {
            if (!$order instanceof Struct\Order) {
                throw new \RuntimeException('$orders MUST be an instance of \\Mosaic\\SDK\\Struct\\Order.');
            }
            $dispatcher->verify($order);
        }
    }
}
