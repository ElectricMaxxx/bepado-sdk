<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK;

/**
 * Abstract base class for revision providers
 *
 * @version $Revision$
 */
abstract class RevisionProvider
{
    /**
     * Get next revision
     *
     * @return string
     */
    abstract public function next();
}
