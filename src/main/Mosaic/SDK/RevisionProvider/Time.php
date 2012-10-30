<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\RevisionProvider;

use Mosaic\SDK\RevisionProvider;

/**
 * Time and iteration based revision provider, which provides ordered revisions 
 * for non-clustered systems.
 *
 * @version $Revision$
 */
class Time extends RevisionProvider
{
    /**
     * Start time of current run
     *
     * @var int
     */
    protected $time = null;

    /**
     * Current iteration
     *
     * @var int
     */
    protected $iteration = 0;

    /**
     * Get next revision
     *
     * @return string
     */
    public function next()
    {
        if (!isset($time)) {
            $this->time = time();
        }

        return sprintf('%d.%05d', $this->time, $this->iteration++);
    }
}
