<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Struct;

use Mosaic\SDK\Struct;

/**
 * Change struct
 *
 * @version $Revision$
 * @api
 */
abstract class Change extends Struct
{
    /**
     * Product ID in source shop
     *
     * @var string
     */
    public $sourceId;

    /**
     * Revision of change
     *
     * @var float
     */
    public $revision;

    /**
     * Verify struct integrity
     *
     * Throws a RuntimeException if integrity is not given.
     *
     * @return void
     */
    public function verify()
    {
        if (empty($this->sourceId)) {
            throw new \RuntimeException('Property $sourceId must be set.');
        }

        if (empty($this->revision)) {
            throw new \RuntimeException('Property $revision must be set.');
        }
    }
}
