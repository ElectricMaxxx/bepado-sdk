<?php

namespace Bepado\SDK\Service;

use Bepado\SDK\Struct;

class PaymentStatusTest extends \PHPUnit_Framework_TestCase
{
    public function testUpdatePaymentStatus()
    {
        $service = new PaymentStatus(
            $productPayments = \Phake::mock('Bepado\SDK\ProductPayments'),
            $shopConfiguration = \Phake::mock('Bepado\SDK\Gateway\ShopConfiguration')
        );

        $service->replicate(array(
            $status = new Struct\PaymentStatus(array(
                'revision' => '1234'
            ))
        ));

        \Phake::verify($productPayments)->updatePaymentStatus($status);
        \Phake::verify($shopConfiguration)->setConfig(PaymentStatus::PAYMENT_REVISION, '1234');
    }
}
