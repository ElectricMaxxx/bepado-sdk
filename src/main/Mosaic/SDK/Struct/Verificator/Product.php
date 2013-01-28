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
class Product extends Verificator
{
    /**
     * Categories
     *
     * @var array
     */
    protected $categories;

    /**
     * Construct from category mapping
     *
     * @param array $categories
     * @return void
     */
    public function __construct(array $categories)
    {
        $this->categories = $categories;
    }

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
        foreach (array(
                'shopId',
                'sourceId',
                'price',
                'currency',
                'availability',
            ) as $property) {
            if ($struct->$property === null) {
                throw new \RuntimeException("Property $property MUST be set in product.");
            }
        }

        if (!count($struct->categories)) {
            throw new \RuntimeException("Assign at least one category to the product.");
        }

        if (count($unknown = array_diff($struct->categories, array_keys($this->categories)))) {
            throw new \RuntimeException("Unknown categories: " . implode(", ", $unknown));
        }
    }
}
