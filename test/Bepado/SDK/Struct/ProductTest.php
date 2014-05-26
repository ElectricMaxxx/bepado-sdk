<?php

namespace Bepado\SDK\Struct;

class ProductTest extends \PHPUnit_Framework_TestCase
{
    public function testFreeDeliveryBackwardsCompatibility()
    {
        $product = new Product();

        $this->assertfalse($product->freeDelivery);
        $product->freeDelivery = true;
    }
}
