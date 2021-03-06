<?php
/**
 * This file is part of the Bepado Common Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK\Rpc\Marshaller\Converter;

use Bepado\SDK\Struct\RpcError;

use Bepado\SDK\Rpc\Marshaller\Converter;

/**
 * Converts any type of exception into an error struct
 */
class ExceptionToErrorConverter extends Converter
{
    /**
     * Converts the given $object to an \Exception.
     *
     * @param mixed $object
     * @return mixed
     */
    public function convertObject($object)
    {
        if ($object instanceof \Exception) {
            return new RpcError(
                array(
                    'message' => $object->getMessage(),
                    'code' => $object->getCode(),
                )
            );
        }

        return $object;
    }
}
