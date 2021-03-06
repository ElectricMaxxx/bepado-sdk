<?php
namespace Bepado\SDK\Rpc;

use Bepado\SDK\Struct;

class LegacyOrderMarshallingTest extends \PHPUnit_Framework_TestCase
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
            array("RpcCalls/Order", "MarshalledXml/Order"),
        );
    }

    /**
     * @param string $rpcCall
     * @param string $xml
     * @dataProvider provideMarshalData
     */
    public function testMarshalToLegacyOrder($rpcCall, $xml)
    {
        $marshaller = new \Bepado\SDK\Rpc\Marshaller\CallMarshaller\XmlCallMarshaller(
            new \Bepado\SDK\XmlHelper(),
            new \Bepado\SDK\Rpc\Marshaller\Converter\LegacyOrderConverter()
        );

        $result = $marshaller->marshal(
            include "{$this->fixturesDirectory}/{$rpcCall}.php"
        );

        $expected = file_get_contents(
            "{$this->expectationsDirectory}/{$xml}.xml"
        );

        $this->assertEquals($expected, $result);
    }

    /**
     * Provide sets of RpcCalls and marshalled XML structs
     *
     * @return array
     */
    public function provideUnmarshalData()
    {
        return array(
            array("MarshalledXml/Order", "RpcCalls/Order"),
        );
    }

    /**
     * @param string $rpcCall
     * @param string $xml
     * @dataProvider provideUnmarshalData
     */
    public function testUnmarshalFromLegacyOrder($xml, $rpcCall)
    {
        $unmarshaller = new \Bepado\SDK\Rpc\Marshaller\CallUnmarshaller\XmlCallUnmarshaller();

        $result = $unmarshaller->unmarshal(
            file_get_contents("{$this->fixturesDirectory}/{$xml}.xml")
        );

        $expected = include "{$this->expectationsDirectory}/{$rpcCall}.php";

        $this->assertEquals($expected, $result);
    }
}
