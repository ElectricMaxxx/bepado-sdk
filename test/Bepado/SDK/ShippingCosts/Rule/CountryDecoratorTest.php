<?php

namespace Bepado\SDK\ShippingCosts\Rule;

use Bepado\SDK\Struct;
use Phake;

class CountryDecoratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_is_applicable_when_from_matching_country()
    {
        $delegatee = Phake::mock('Bepado\SDK\ShippingCosts\Rule');
        Phake::when($delegatee)->isApplicable(\Phake::anyParameters())->thenReturn(true);

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

    /**
     * @test
     */
    public function it_is_not_applicable_when_zip_is_excluded()
    {
        $delegatee = $this->getMock('Bepado\SDK\ShippingCosts\Rule');

        $country = new CountryDecorator(array(
            'countries' => array('DEU'),
            'excludeZipCodes' => array('53'),
            'delegatee' => $delegatee
        ));

        $this->assertFalse(
            $country->isApplicable(
                new Struct\Order(
                    array(
                        'deliveryAddress' => new Struct\Address(array(
                            'country' => 'DEU',
                            'zip' => '53225',
                        ))
                    )
                )
            )
        );
    }
}
