<?php

namespace Bepado\SDK\Struct\Verificator;

use Bepado\SDK\Struct;
use Bepado\SDK\ShippingRuleParser;

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
            'vendor' => 'Foo',
        ));
    }

    public function setUp()
    {
        $this->dispatcher = \Phake::mock('Bepado\SDK\Struct\VerificatorDispatcher');
        $this->verificator = new Product(
            new ShippingRuleParser\Google()
        );
    }

    private function verify($product)
    {
        $this->verificator->verify($this->dispatcher, $product);
    }

    public function testValidProduct()
    {
        $this->verify($this->createValidProduct());
    }

    public function testVendorEmptyIsError()
    {
        $product = $this->createValidProduct();
        $product->vendor = null;

        $this->setExpectedException('RuntimeException', 'Property vendor MUST be non-empty.');
        $this->verify($product);
    }

    public function testTitleEmptyIsError()
    {
        $product = $this->createValidProduct();
        $product->title = null;

        $this->setExpectedException('RuntimeException', 'Property title MUST be non-empty.');
        $this->verify($product);
    }

    public function testInvalidShippingRule()
    {
        $product = $this->createValidProduct();
        $product->shipping = "Invalid:::";

        $this->setExpectedException('RuntimeException', 'Unexpected Delivery name (free text) at position 0 – expected one of: Element separator ":", Country Code (ISO 3166-1) (eg. DE)');
        $this->verify($product);
    }

    public function testValidUnitAttributes()
    {
        $product = $this->createValidProduct();
        $product->attributes[Struct\Product::ATTRIBUTE_UNIT] = 'kg';
        $product->attributes[Struct\Product::ATTRIBUTE_QUANTITY] = 10;
        $product->attributes[Struct\Product::ATTRIBUTE_REFERENCE_QUANTITY] = 100;

        $this->verify($product);
    }

    /**
     * @dataProvider dataValidDimensions
     */
    public function testValidDimensions($dimension)
    {
        $product = $this->createValidProduct();
        $product->attributes[Struct\Product::ATTRIBUTE_DIMENSION] = $dimension;

        $this->verify($product);
    }

    /**
     * @dataProvider dataInvalidDimensions
     */
    public function testInvalidDimensions($dimension)
    {
        $product = $this->createValidProduct();
        $product->attributes[Struct\Product::ATTRIBUTE_DIMENSION] = $dimension;

        $this->setExpectedException('RuntimeException', 'Product Dimensions Attribute has to be in format ');
        $this->verify($product);
    }

    static public function dataValidDimensions()
    {
        return array(
            array('10x20x30'),
            array('10.5x20.7x30.2'),
            array('1.5x20x30'),
            array('1x20.5x30'),
            array('1x20x30.7'),
        );
    }

    static public function dataInvalidDimensions()
    {
        return array(
            array('axbxc'),
            array('10x10'),
            array('10,4x10,4x10,4'),
        );
    }

    /**
     * @param mixed $images
     * @dataProvider dataValidImages
     */
    public function testValidImages($images)
    {
        $product = $this->createValidProduct();

        $product->images = $images;

        $this->verify($product);
    }

    static public function dataValidImages()
    {
        return array(
            array(
                array()
            ),
            array(
                array('foo', 'bar')
            ),
            array(
                array(0 => 'foo', 1 => 'bar')
            ),
        );
    }

    /**
     * @param mixed $images
     * @dataProvider dataInvalidImagesBasetype
     */
    public function testInvalidImagesBasetype($images)
    {
        $product = $this->createValidProduct();

        $product->images = $images;

        $this->setExpectedException('RuntimeException', 'Product#images must be an array.');
        $this->verify($product);
    }

    static public function dataInvalidImagesBasetype()
    {
        return array(
            array(
                'foo'
            ),
            array(
                new \ArrayObject()
            ),
            array(
                null
            ),
        );
    }

    /**
     * @param mixed $images
     * @dataProvider dataInvalidImagesIndexing
     */
    public function testInvalidImagesIndexing($images)
    {
        $product = $this->createValidProduct();

        $product->images = $images;

        $this->setExpectedException('RuntimeException', 'Product#images must be numerically indexed starting with 0.');
        $this->verify($product);
    }

    static public function dataInvalidImagesIndexing()
    {
        return array(
            array(
                array('foo' => 'foo', 'bar' => 'bar')
            ),
            array(
                array(1 => 'foo', 2 => 'bar')
            ),
        );
    }
}
