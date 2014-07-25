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
    public function testMarshalRpcCall($rpcCall, $xml)
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
}
