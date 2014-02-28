<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * @version $Revision$
 */

namespace Bepado\SDK\Struct\Metric;

use \Bepado\SDK\Struct\Metric;

/**
 * Time metric
 *
 * @version $Revision$
 */
class Time extends Metric
{
    /**
     * Time value
     *
     * @var int
     */
    public $time;

    public function __toString()
    {
        return sprintf(
            " METRIC_TIME metric=%s value=%F ",
            $this->name,
            $this->time
        );
    }
}
