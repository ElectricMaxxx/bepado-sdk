<?php

namespace Bepado\SDK\Service;

use Bepado\SDK\HttpClient\Response;

class OrderStatusUpdateTest extends \PHPUnit_Framework_TestCase
{
    const APIKEY = 'abcdefg';

    public function testUpdate()
    {
        $expectedRequest = array(
            'apiKey' => self::APIKEY,
            'remoteOrderId' => 1,
            'orderStatus' => 'open',
            'messages' => array(),
        );

        $client = $this->getMock('Bepado\SDK\HttpClient');
        $client
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('POST'),
                $this->equalTo('/sdk/update-order-status'),
                $this->equalTo(json_encode($expectedRequest))
            )
            ->will($this->returnValue(new Response(array('status' => 200))))
        ;

        $orderStatusUpdate = new OrderStatusUpdate(
            $client, self::APIKEY
        );

        $orderStatusUpdate->update(1, 'open');
    }

    public function testUpdateInvalidOrderStatus()
    {
        $client = $this->getMock('Bepado\SDK\HttpClient');

        $orderStatusUpdate = new OrderStatusUpdate(
            $client, self::APIKEY
        );

        $this->setExpectedException('InvalidArgumentException');
        $orderStatusUpdate->update(1, 'invalid');
    }
}
