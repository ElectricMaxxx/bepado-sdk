<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK;

use Mosaic\SDK\Struct\Product;

/**
 * Abstract base class to store SDK related data
 *
 * You may create custom extensions of this class, if the default data stores
 * do not work for you.
 *
 * @version $Revision$
 * @api
 */
abstract class Gateway implements Gateway\ChangeGateway, Gateway\ProductGateway, Gateway\Revision, Gateway\ShopConfiguration
{
}
