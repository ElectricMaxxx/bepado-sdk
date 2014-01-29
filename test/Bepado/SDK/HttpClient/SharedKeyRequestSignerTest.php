<?php

namespace Bepado\SDK\HttpClient;

use Bepado\SDK\Struct\ShopConfiguration;

class SharedKeyRequestSignerTest extends \PHPUnit_Framework_TestCase
{
    private $gatewayMock;

    public function setUp()
    {
        $this->gatewayMock = $this->getMock('Bepado\SDK\Gateway\ShopConfiguration');
    }

    public function testSignRequest()
    {
        $this->gatewayMock->expects($this->any())
            ->method('getShopConfiguration')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new ShopConfiguration(array('key' => 1234))));

        $this->gatewayMock->expects($this->once())
            ->method('getShopId')
            ->will($this->returnValue(1337));

        $time = 1234567890;

        $clock = $this->getMock('Bepado\SDK\Service\Clock');
        $clock->expects($this->once())->method('time')->will($this->returnValue($time));

        $signer = new SharedKeyRequestSigner($this->gatewayMock, $clock, null);

        $headers = $signer->signRequest(42, '<xml body>');

        $this->assertEquals(array(
                'Authorization: SharedKey party="1337",nonce="de93510785d31758983da9a65fd7216c280cd41248a26ff25af037c97a4b31fb0a63fa2906b763b31601448f6cc3563c9c3afa4dcf557fa714129af302780f7a"',
                'X-Bepado-Authorization: SharedKey party="1337",nonce="de93510785d31758983da9a65fd7216c280cd41248a26ff25af037c97a4b31fb0a63fa2906b763b31601448f6cc3563c9c3afa4dcf557fa714129af302780f7a"',
                'Date: Fri, 13 Feb 2009 23:31:30 GMT',
            ), $headers);
    }

    public function testVerifyBepadoRequest()
    {
        $this->gatewayMock->expects($this->never())->method('getShopConfiguration');

        $clock = $this->getMock('Bepado\SDK\Service\Clock');

        $signer = new SharedKeyRequestSigner($this->gatewayMock, $clock, "aaa-bbb-ccc-ddd");
        $token = $signer->verifyRequest(
            '<xml body>',
            array(
                'HTTP_AUTHORIZATION' => 'SharedKey party="bepado",nonce="800b055230b317aa24bc27c02ee02997cbfdf7969deda30804d55a6d59a3fcb528cf971e25033b6b37b4e99bcdd4b95de56352f486d1ebb63b5a2d4d42b41eef"',
                'HTTP_DATE' => 'Fri, 13 Feb 2009 23:31:30 GMT'
            )
        );

        $this->assertTrue($token->authenticated, "Authorization Header is valid");
        $this->assertEquals("bepado", $token->userIdentifier);
    }

    public function testVerifyBepadoRequestFallbackCustomAuthHeader()
    {
        $this->gatewayMock->expects($this->never())->method('getShopConfiguration');

        $clock = $this->getMock('Bepado\SDK\Service\Clock');

        $signer = new SharedKeyRequestSigner($this->gatewayMock, $clock, "aaa-bbb-ccc-ddd");
        $token = $signer->verifyRequest(
            '<xml body>',
            array(
                'HTTP_X_BEPADO_AUTHORIZATION' => 'SharedKey party="bepado",nonce="800b055230b317aa24bc27c02ee02997cbfdf7969deda30804d55a6d59a3fcb528cf971e25033b6b37b4e99bcdd4b95de56352f486d1ebb63b5a2d4d42b41eef"',
                'HTTP_DATE' => 'Fri, 13 Feb 2009 23:31:30 GMT'
            )
        );

        $this->assertTrue($token->authenticated, "Authorization Header is valid");
        $this->assertEquals("bepado", $token->userIdentifier);
    }

    public function testVerifyShopRequest()
    {
        $this->gatewayMock->expects($this->once())
            ->method('getShopConfiguration')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new ShopConfiguration(array('key' => 1234))));

        $clock = $this->getMock('Bepado\SDK\Service\Clock');

        $signer = new SharedKeyRequestSigner($this->gatewayMock, $clock, "aaa-bbb-ccc-ddd");
        $token = $signer->verifyRequest(
            '<xml body>',
            array(
                'HTTP_AUTHORIZATION' => 'SharedKey party="42",nonce="de93510785d31758983da9a65fd7216c280cd41248a26ff25af037c97a4b31fb0a63fa2906b763b31601448f6cc3563c9c3afa4dcf557fa714129af302780f7a"',
                'HTTP_DATE' => 'Fri, 13 Feb 2009 23:31:30 GMT'
            )
        );

        $this->assertTrue($token->authenticated, "Authorization Header is valid");
        $this->assertEquals(42, $token->userIdentifier);
    }

    public function testVerifyShopRequestFallbackCustomAuthHeader()
    {
        $this->gatewayMock->expects($this->once())
            ->method('getShopConfiguration')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new ShopConfiguration(array('key' => 1234))));

        $clock = $this->getMock('Bepado\SDK\Service\Clock');

        $signer = new SharedKeyRequestSigner($this->gatewayMock, $clock, "aaa-bbb-ccc-ddd");
        $token = $signer->verifyRequest(
            '<xml body>',
            array(
                'HTTP_X_BEPADO_AUTHORIZATION' => 'SharedKey party="42",nonce="de93510785d31758983da9a65fd7216c280cd41248a26ff25af037c97a4b31fb0a63fa2906b763b31601448f6cc3563c9c3afa4dcf557fa714129af302780f7a"',
                'HTTP_DATE' => 'Fri, 13 Feb 2009 23:31:30 GMT'
            )
        );

        $this->assertTrue($token->authenticated, "Authorization Header is valid");
        $this->assertEquals(42, $token->userIdentifier);
    }
}
