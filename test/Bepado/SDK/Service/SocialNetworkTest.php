<?php

namespace Bepado\SDK\Service;

use Bepado\SDK\HttpClient\Response;

class SocialNetworkTest extends \PHPUnit_Framework_TestCase
{
    const APIKEY = 'abcdefg';

    public function testUpdateOrderStatus()
    {
        $status = new \Bepado\SDK\Struct\OrderStatus(array(
            'id' => 'abcdefg',
            'status' => \Bepado\SDK\Struct\OrderStatus::STATE_OPEN,
        ));
        $client = \Phake::mock('Bepado\SDK\HttpClient');

        \Phake::when($client)->request(
            'POST',
            '/sdk/update-order-status',
            json_encode($status),
            array(
                'Content-Type: application/json',
                'X-Bepado-Shop: 1',
                'X-Bepado-Key: ' . hash_hmac('sha512', json_encode($status), self::APIKEY)
            )
        )->thenReturn(new Response(array('status' => 200)));

        $dispatcher = \Phake::mock('Bepado\SDK\Struct\VerificatorDispatcher');

        $socialNetwork = new SocialNetwork($client, $dispatcher, 1, self::APIKEY);
        $socialNetwork->updateOrderStatus($status);
    }
}
