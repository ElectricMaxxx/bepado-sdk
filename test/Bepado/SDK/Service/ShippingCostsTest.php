<?php

namespace Bepado\SDK\Service;

class ShippingCostsTest extends \PHPUnit_Framework_TestCase
{
    public function testMigrateOldVatDefinition()
    {
        $gateway = \Phake::mock('Bepado\SDK\Gateway\ShippingCosts');
        $calculator = \Phake::mock('Bepado\SDK\ShippingCostCalculator');

        $service = new ShippingCosts($gateway, $calculator);

        $rules = new \Bepado\SDK\ShippingCosts\Rules();
        $rules->vatMode = 'fix';
        $rules->vat = 0.20;

        \Phake::when($gateway)->getShippingCosts(\Phake::anyParameters())->thenReturn($rules);

        $rules = $service->getShippingCostRules(1, 2, 3);

        $this->assertEquals('fix', $rules->vatConfig->mode);
        $this->assertEquals(0.2, $rules->vatConfig->vat);
    }
}
