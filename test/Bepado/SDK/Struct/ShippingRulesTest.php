<?php

namespace Bepado\SDK\Struct;

class ShippingRulesTest extends \PHPUnit_Framework_TestCase
{
    static public function dataFromString()
    {
        return array(
            array(
                'DE::Standard:4.95 EUR,DE::Express:10.00 EUR',
                new ShippingRules(array(
                    'rules' => array(
                        new ShippingRule(array(
                            'country' => 'DE',
                            'region' => '',
                            'service' => 'Standard',
                            'price' => 4.95,
                            'currency' => 'EUR'
                        )),
                        new ShippingRule(array(
                            'country' => 'DE',
                            'region' => '',
                            'service' => 'Express',
                            'price' => 10.00,
                            'currency' => 'EUR'
                        )),
                    )
                )),
                'DE::Standard:4.95 EUR,DE::Express:10.00 EUR',
            ),
            array(
                'DE:53*:Standard P3D:4.95 EUR',
                new ShippingRules(array(
                    'rules' => array(
                        new ShippingRule(array(
                            'country' => 'DE',
                            'region' => '53*',
                            'service' => 'Standard',
                            'deliveryWorkDays' => 3,
                            'price' => 4.95,
                            'currency' => 'EUR'
                        )),
                    )
                )),
                'DE:53*:Standard P3D:4.95 EUR',
            ),
            array(
                'DE:53*:Standard PT24H:4.95 EUR',
                new ShippingRules(array(
                    'rules' => array(
                        new ShippingRule(array(
                            'country' => 'DE',
                            'region' => '53*',
                            'service' => 'Standard',
                            'deliveryWorkDays' => 1,
                            'price' => 4.95,
                            'currency' => 'EUR'
                        )),
                    )
                )),
                'DE:53*:Standard P1D:4.95 EUR',
            ),
        );
    }

    /**
     * @dataProvider dataFromString
     */
    public function testFromString($original, $expected, $reserialized)
    {
        $actual = ShippingRules::fromString($original);
        $this->assertEquals($expected, $actual);
        $this->assertEquals($reserialized, (string)$actual);
    }

    public function testEmptyShippingThrowsException()
    {
        $this->setExpectedException('Bepado\SDK\Exception\InvalidArgumentException');

        ShippingRules::fromString('');
    }

    public function testInvalidShippingRuleThrowsException()
    {
        $this->setExpectedException('Bepado\SDK\Exception\InvalidArgumentException', 'Invalid shipping rule passed "DE:", required to be seperated by 3 double-colons, ex: DE:::3.99 EUR');

        ShippingRules::fromString('DE:');
    }
}
