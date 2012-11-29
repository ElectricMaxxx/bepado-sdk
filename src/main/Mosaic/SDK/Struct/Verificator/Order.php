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

use Mosaic\SDK\Struct\OrderItem;
use Mosaic\SDK\Struct\Address;

/**
 * Visitor verifying integrity of struct classes
 *
 * @version $Revision$
 */
class Order extends Verificator
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
        if (!is_array($struct->products)) {
            throw new \RuntimeException('Products MUST be an array.');
        }

        foreach ($struct->products as $product) {
            if (!$product instanceof OrderItem) {
                throw new \RuntimeException(
                    'Products array MUST contain only instances of \\Mosaic\\SDK\\Struct\\OrderItem.'
                );
            }

            $dispatcher->verify($product);
        }

        if (!$struct->deliveryAddress instanceof Address) {
            throw new \RuntimeException('Delivery address MUST be an instance of \\Mosaic\\SDK\\Struct\\Address.');
        }
        $dispatcher->verify($struct->deliveryAddress);
    }
}
