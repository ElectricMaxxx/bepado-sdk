<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK;

/**
 * Base class for change visitor implementations
 *
 * @version $Revision$
 */
abstract class ChangeVisitor
{
    /**
     * Visit changes
     *
     * @param array $changes
     * @return array
     */
    abstract public function visit(array $changes);
}
