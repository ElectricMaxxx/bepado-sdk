<?php

namespace Bepado\SDK\ShippingCosts\Rule;

use Bepado\SDK\Struct;

class CountryDecoratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_is_applicable_when_from_matching_country()
    {
        $delegatee = $this->getMock('Bepado\SDK\ShippingCosts\Rule');

        $country = new CountryDecorator(array(
            'countries' => array('DEU'),
            'delegatee' => $delegatee
        ));

        $this->assertTrue(
            $country->isApplicable(
                new Struct\Order(
                    array(
                        'deliveryAddress' => new Struct\Address(array(
                            'country' => 'DEU',
                        ))
                    )
                )
            )
        );
    }
}
