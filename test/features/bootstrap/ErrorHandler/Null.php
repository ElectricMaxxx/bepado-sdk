<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * @version $Revision$
 */

namespace Bepado\SDK\ErrorHandler;

use Bepado\SDK\ErrorHandler;
use Bepado\SDK\Struct;

/**
 * Base class for logger implementations
 *
 * @version $Revision$
 */
class Null extends ErrorHandler
{
    /**
     * Handle error
     *
     * @param Struct\Error $error
     * @return void
     */
    public function handleError(Struct\Error $error)
    {
        // Don't do nothing
    }

    /**
     * Handle exception
     *
     * @param \Exception $exception
     * @return void
     */
    public function handleException(\Exception $exception)
    {
        // Don't do nothing
    }
}
