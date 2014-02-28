<?php
namespace Bepado\SDK\Rpc;

use Bepado\SDK\Struct;

class MarshallerTest extends \PHPUnit_Framework_TestCase
{
    private $fixturesDirectory;

    private $expectationsDirectory;

    protected function setUp()
    {
        parent::setUp();

        $this->fixturesDirectory = __DIR__ . '/../../../_fixtures';
        $this->expectationsDirectory = __DIR__ . '/../../../_expectations';
    }

    /**
     * Provide sets of RpcCalls and marshalled XML structs
     *
     * @return array
     */
    public function provideMarshalData()
    {
        return array(
            array("RpcCalls/Empty", "MarshalledXml/Empty"),
            array("RpcCalls/Integer", "MarshalledXml/Integer"),
            array("RpcCalls/Boolean", "MarshalledXml/Boolean"),
            array("RpcCalls/Null", "MarshalledXml/Null"),
            array("RpcCalls/Float", "MarshalledXml/Float"),
            array("RpcCalls/String", "MarshalledXml/String"),
            array("RpcCalls/EmptyArray", "MarshalledXml/EmptyArray"),
            array("RpcCalls/Array", "MarshalledXml/Array"),
            array("RpcCalls/Product", "MarshalledXml/Product"),
            array("RpcCalls/Mixed", "MarshalledXml/Mixed"),
        );
    }

    /**
     * @param string $rpcCall
     * @param string $xml
     * @dataProvider provideMarshalData
     */
    public function testMarshalRpcCall($rpcCall, $xml)
    {
        $marshaller = new \Bepado\SDK\Rpc\Marshaller\CallMarshaller\XmlCallMarshaller(
            new \Bepado\SDK\XmlHelper()
        );

        $this->assertMarshallEquals($marshaller, $rpcCall, $xml);
    }

    public function provideSimpleClassMapMarshalData()
    {
        return array(
            array("RpcCalls/Product", "MarshalledXml/SimpleClassMapProduct"),
            array("RpcCalls/Mixed", "MarshalledXml/SimpleClassMapMixed"),
        );
    }

    /**
     * @param $rpcCall
     * @param $xml
     * @dataProvider provideSimpleClassMapMarshalData
     */
    public function testMarshalObjectWithSimpleClassMapConversion($rpcCall, $xml) {
        $marshaller = new \Bepado\SDK\Rpc\Marshaller\CallMarshaller\XmlCallMarshaller(
            new \Bepado\SDK\XmlHelper(),
            new \Bepado\SDK\Rpc\Marshaller\Converter\SimpleClassMapConverter(
                array(
                    'Bepado\\SDK\\Rpc\\ShopProduct' =>
                        'Bepado\\SDK\\Struct\\Product'
                )
            )
        );

        $this->assertMarshallEquals($marshaller, $rpcCall, $xml);
    }

    public function provideChainingConverterMarshalData()
    {
        return array(
            // Intentionally simple results, since these should not change!
            array("RpcCalls/Product", "MarshalledXml/SimpleClassMapProduct"),
            array("RpcCalls/Mixed", "MarshalledXml/SimpleClassMapMixed"),
            // Specialized
            array("RpcCalls/Exception", "MarshalledXml/ChainedMapException"),
            array("RpcCalls/InvalidArgumentException", "MarshalledXml/ChainedMapInvalidArgumentException"),
        );
    }

    /**
     * @param $rpcCall
     * @param $xml
     * @dataProvider provideChainingConverterMarshalData
     */
    public function testMarshalExceptionsChainingConversion($rpcCall, $xml)
    {
        $marshaller = new \Bepado\SDK\Rpc\Marshaller\CallMarshaller\XmlCallMarshaller(
            new \Bepado\SDK\XmlHelper(),
            new \Bepado\SDK\Rpc\Marshaller\Converter\ChainingConverter(
                array(
                    new \Bepado\SDK\Rpc\Marshaller\Converter\ExceptionToErrorConverter(),
                    new \Bepado\SDK\Rpc\Marshaller\Converter\SimpleClassMapConverter(
                        array(
                            'Bepado\\SDK\\Rpc\\ShopProduct' =>
                                'Bepado\\SDK\\Struct\\Product'
                        )
                    )
                )
            )
        );

        $this->assertMarshallEquals($marshaller, $rpcCall, $xml);
    }

    /**
     * Provide sets of marshalled XML and RpcCall structs
     *
     * @return array
     */
    public function provideUnmarshalData()
    {
        return array(
            array("MarshalledXml/Empty", "RpcCalls/Empty"),
            array("MarshalledXml/Integer", "RpcCalls/Integer"),
            array("MarshalledXml/Boolean", "RpcCalls/Boolean"),
            array("MarshalledXml/Null", "RpcCalls/Null"),
            array("MarshalledXml/Float", "RpcCalls/Float"),
            array("MarshalledXml/String", "RpcCalls/String"),
            array("MarshalledXml/EmptyArray", "RpcCalls/EmptyArray"),
            array("MarshalledXml/Array", "RpcCalls/Array"),
            array("MarshalledXml/ArrayWithoutKey", "RpcCalls/Array"),
            array("MarshalledXml/Product", "RpcCalls/Product"),
            array("MarshalledXml/Mixed", "RpcCalls/Mixed"),
        );
    }

    /**
     * @param string $xml
     * @param string $rpcCall
     * @dataProvider provideUnmarshalData
     */
    public function testUnmarshalRpcCall($xml, $rpcCall)
    {
        $unmarshaller = new \Bepado\SDK\Rpc\Marshaller\CallUnmarshaller\XmlCallUnmarshaller();

        $this->assertUnmarshallEquals($unmarshaller, $rpcCall, $xml);
    }

    public function provideSimpleClassMapUnmarshalData()
    {
        return array(
            array("MarshalledXml/Product", "RpcCalls/SimpleClassMapProduct"),
            array("MarshalledXml/Mixed", "RpcCalls/SimpleClassMapMixed"),
        );
    }

    /**
     * @param string $xml
     * @param string $rpcCall
     * @dataProvider provideSimpleClassMapUnmarshalData
     */
    public function testUnmarshalObjectWithSimpleClassMapConversion ($xml, $rpcCall)
    {
        $unmarshaller = new \Bepado\SDK\Rpc\Marshaller\CallUnmarshaller\XmlCallUnmarshaller(
            new \Bepado\SDK\Rpc\Marshaller\Converter\SimpleClassMapConverter(
                array(
                    'Bepado\\SDK\\Struct\\Product' =>
                    'Bepado\\SDK\\Rpc\\ShopProduct'
                )
            )
        );

        $this->assertUnmarshallEquals($unmarshaller, $rpcCall, $xml);
    }

    public function provideErrorUnmarshalData()
    {
        return array(
            array("MarshalledXml/Exception"),
            array("MarshalledXml/InvalidArgumentException"),
        );
    }

    /**
     * @param string $xml
     * @param string $rpcCall
     * @dataProvider provideErrorUnmarshalData
     * @expectedException \Exception
     */
    public function testUnmarshalError($xml)
    {
        $unmarshaller = new \Bepado\SDK\Rpc\Marshaller\CallUnmarshaller\XmlCallUnmarshaller(
            new \Bepado\SDK\Rpc\Marshaller\Converter\ChainingConverter(
                array(
                    new \Bepado\SDK\Rpc\Marshaller\Converter\ErrorToExceptionConverter(),
                    new \Bepado\SDK\Rpc\Marshaller\Converter\SimpleClassMapConverter(
                        array(
                            'Bepado\\SDK\\Struct\\Product' =>
                            'Bepado\\SDK\\Rpc\\Product\\ShopProduct'
                        )
                    )
                )
            )
        );

        $result = $unmarshaller->unmarshal(
            file_get_contents(
                "{$this->fixturesDirectory}/{$xml}.xml"
            )
        );
    }

    public function testUnmarshallIgnoreNonExistantProperties()
    {
        $unmarshaller = new \Bepado\SDK\Rpc\Marshaller\CallUnmarshaller\XmlCallUnmarshaller(
            new \Bepado\SDK\Rpc\Marshaller\Converter\SimpleClassMapConverter(
                array(
                    'Bepado\\SDK\\Struct\\Product' =>
                    'Bepado\\SDK\\Rpc\\TestSmallProduct'
                )
            )
        );

        $result = $unmarshaller->unmarshal(
            file_get_contents(
                "{$this->fixturesDirectory}/MarshalledXml/Product.xml"
            )
        );

        $this->assertInstanceOf('Bepado\SDK\Rpc\TestSmallProduct', $result->arguments[0]);
        $this->assertEquals('Secret pH Balanced INVISIBLE Solid powder fresh', $result->arguments[0]->title);
        $this->assertEquals('4.99', $result->arguments[0]->price);
    }

    public function testUnmarshallNonStructClassThrowsException()
    {
        $unmarshaller = new \Bepado\SDK\Rpc\Marshaller\CallUnmarshaller\XmlCallUnmarshaller(
            new \Bepado\SDK\Rpc\Marshaller\Converter\SimpleClassMapConverter(
                array(
                    'Bepado\\SDK\\Struct\\Product' =>
                    'Bepado\\SDK\\Rpc\\TestSmallProduct'
                )
            )
        );

        $this->setExpectedException('RuntimeException', 'Cannot unmarshall non-Struct classes.');

        $unmarshaller->unmarshal(
            file_get_contents(
                "{$this->fixturesDirectory}/MarshalledXml/InvalidStruct.xml"
            )
        );
    }

    /**
     * @group BEP-534
     */
    public function testInvalidBomsAreStripped()
    {
        $unmarshaller = new \Bepado\SDK\Rpc\Marshaller\CallUnmarshaller\XmlCallUnmarshaller(
            new \Bepado\SDK\Rpc\Marshaller\Converter\SimpleClassMapConverter(array())
        );

        $data = $unmarshaller->unmarshal(
            file_get_contents(
                "{$this->fixturesDirectory}/customer_error1.xml"
            )
        );

        $this->assertInstanceOf('Bepado\SDK\Struct\RpcCall', $data);
    }

    private function assertMarshallEquals($marshaller, $rpcCall, $xml)
    {
        $result = $marshaller->marshal(
            include "{$this->fixturesDirectory}/{$rpcCall}.php"
        );

        if (isset($_ENV['__TEST_GENERATE_FIXTURE'])) {
            file_put_contents(
                "{$this->expectationsDirectory}/{$xml}.xml",
                $result
            );
        }

        $expected = file_get_contents(
            "{$this->expectationsDirectory}/{$xml}.xml"
        );

        $this->assertEquals($expected, $result);
    }

    private function assertUnmarshallEquals($unmarshaller, $rpcCall, $xml)
    {
        $result = $unmarshaller->unmarshal(
            file_get_contents(
                "{$this->fixturesDirectory}/{$xml}.xml"
            )
        );

        $expected = include "{$this->expectationsDirectory}/{$rpcCall}.php";

        $this->assertEquals($expected, $result);
    }
}

class TestSmallProduct
{
    public $title;
    public $price;
}

class ShopProduct extends Struct
{
    public $shopId;
    public $sourceId;
    public $masterId;
    public $approved = false;
    public $purchasePrice;
    public $fixedPrice;
    public $grossMarginPercent;
    public $freeDelivery = false;
    public $deliveryDate;
    public $relevance = 0;
    public $productId;
    public $revisionId;
    public $language = 'de_DE';
    public $ean;
    public $url;
    public $title;
    public $shortDescription;
    public $longDescription;
    public $vendor;
    public $vat = 0.19;
    public $price;
    public $currency;
    public $availability;
    public $images = array();
    public $categories = array();
    public $attributes = array();
}
