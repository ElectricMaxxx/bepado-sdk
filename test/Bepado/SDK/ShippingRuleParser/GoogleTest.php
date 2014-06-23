<?php

namespace Bepado\SDK\ShippingRuleParser;

use Bepado\SDK\Struct\ShippingRules;
use Bepado\SDK\Struct\ShippingRule;

class GoogleTest extends \PHPUnit_Framework_TestCase
{
    public function dataFromString()
    {
        return array(
            array(
                '',
                null,
            ),
            array(
                ':::7.95 USD',
                new ShippingRules(array(
                    'rules' => array(
                        new ShippingRule(array(
                            'price' => 7.95,
                            'currency' => 'USD'
                        )),
                    )
                )),
            ),
            array(
                'US:::7.95 USD',
                new ShippingRules(array(
                    'rules' => array(
                        new ShippingRule(array(
                            'country' => 'US',
                            'price' => 7.95,
                            'currency' => 'USD'
                        )),
                    )
                )),
            ),
            array(
                'US:MA:Ground:5.95 USD,US:024*:Ground:7.95 USD',
                new ShippingRules(array(
                    'rules' => array(
                        new ShippingRule(array(
                            'country' => 'US',
                            'region' => 'MA',
                            'service' => 'Ground',
                            'price' => 5.95,
                            'currency' => 'USD'
                        )),
                        new ShippingRule(array(
                            'country' => 'US',
                            'zipRange' => '024*',
                            'service' => 'Ground',
                            'price' => 7.95,
                            'currency' => 'USD'
                        )),
                    )
                )),
            ),
            array(
                'DE::Standard:4.95 EUR,DE::Express:10.00 EUR',
                new ShippingRules(array(
                    'rules' => array(
                        new ShippingRule(array(
                            'country' => 'DE',
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
                ))
            ),
            array(
                'DE:53*:Standard [3D]:4.95 EUR',
                new ShippingRules(array(
                    'rules' => array(
                        new ShippingRule(array(
                            'country' => 'DE',
                            'zipRange' => '53*',
                            'service' => 'Standard',
                            'deliveryWorkDays' => 3,
                            'price' => 4.95,
                            'currency' => 'EUR'
                        )),
                    )
                ))
            ),
            array(
                'DE:53*:Standard [24H]:4.95 EUR',
                new ShippingRules(array(
                    'rules' => array(
                        new ShippingRule(array(
                            'country' => 'DE',
                            'zipRange' => '53*',
                            'service' => 'Standard',
                            'deliveryWorkDays' => 1,
                            'price' => 4.95,
                            'currency' => 'EUR'
                        )),
                    )
                ))
            ),
        );
    }

    /**
     * @dataProvider dataFromString
     */
    public function testFromString($original, $expected)
    {
        $parser = new Google();

        $actual = $parser->parseString($original);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Get parse errors
     *
     * @return array
     */
    public function getParseErrors()
    {
        return array(
            array(
                ':',
                'Unexpected T_EOF at position 1 – expected one of: T_ELEMENT_SEPARATOR'
            ),
            array(
                ':::',
                'Unexpected T_EOF at position 3 – expected one of: T_PRICE'
            ),
            array(
                'invalid:::',
                'Unexpected T_DELIVERY_NAME at position 0 – expected one of: T_ELEMENT_SEPARATOR, T_COUNTRY'
            ),
            array(
                ':foo::',
                'Unexpected T_DELIVERY_NAME at position 1 – expected one of: T_ELEMENT_SEPARATOR, T_ZIP, T_REGION, T_COUNTRY'
            ),
            array(
                '::foo []:',
                'Unexpected T_STRING at position 6 – expected one of: T_ELEMENT_SEPARATOR, T_DELIVERY_NAME'
            ),
        );
    }

    /**
     * @dataProvider getParseErrors
     */
    public function testParseError($input, $error)
    {
        $this->setExpectedException('UnexpectedValueException', $error);

        $parser = new Google();
        $parser->parseString($input);
    }
}
