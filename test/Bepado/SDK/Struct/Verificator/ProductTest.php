<?php

namespace Bepado\SDK\Struct\Verificator;

use Bepado\SDK\Struct;

class ProductTest extends \PHPUnit_Framework_TestCase
{
    private $verificator;

    private function createValidProduct()
    {
        return new Struct\Product(array(
            'shopId' => 10,
            'sourceId' => 10,
            'title' => 'Foo',
            'price' => 10.10,
            'purchasePrice' => 20.20,
            'availability' => 0,
        ));
    }

    public function setUp()
    {
        $this->dispatcher = \Phake::mock('Bepado\SDK\Struct\VerificatorDispatcher');
        $this->verificator = new Product();
    }

    private function verify($product)
    {
        $this->verificator->verify($this->dispatcher, $product);
    }

    public function testValidProduct()
    {
        $this->verify($this->createValidProduct());
    }

    public function testValidUnitAttributes()
    {
        $product = $this->createValidProduct();
        $product->attributes[Struct\Product::ATTRIBUTE_UNIT] = 'kg';
        $product->attributes[Struct\Product::ATTRIBUTE_QUANTITY] = 10;
        $product->attributes[Struct\Product::ATTRIBUTE_REFERENCE_QUANTITY] = 100;

        $this->verify($this->createValidProduct());
    }
}
