<?php
/**
 * This file is part of the Bepado SDK Component.
 *
 * The SDK is licensed under MIT license. (c) Shopware AG and Qafoo GmbH
 */

namespace Bepado\SDK\Struct;

use Bepado\Common;

class AddressTest extends \PHPUnit_Framework_TestCase
{
    public function getValidNames()
    {
        return array(
            array('Kore Nordmann', 'Kore', '', 'Nordmann'),
            array('Kore D. Nordmann', 'Kore', 'D.', 'Nordmann'),
            array('Kore D. E. F. Nordmann', 'Kore', 'D. E. F.', 'Nordmann'),
        );
    }

    /**
     * @dataProvider getValidNames
     */
    public function testSetName($name, $firstName, $middleName, $surName)
    {
        $address = new Address(
            array(
                'name' => $name,
            )
        );

        $this->assertSame($firstName, $address->firstName);
        $this->assertSame($middleName, $address->middleName);
        $this->assertSame($surName, $address->surName);
    }

    /**
     * @dataProvider getValidNames
     */
    public function testSetAndGetName($name)
    {
        $address = new Address(
            array(
                'name' => $name,
            )
        );

        $this->assertSame($name, $address->name);
    }

    /**
     * @expectedException \DomainException
     */
    public function testSetInvalidName()
    {
        $address = new Address(
            array(
                'name' => 'Invalid',
            )
        );
    }

    public function getValidStreets()
    {
        return array(
            array('Musterweg 1', 'Musterweg', '1'),
            array('Muster Weg 1', 'Muster Weg', '1'),
            array('Musterweg 23a', 'Musterweg', '23a'),
        );
    }

    /**
     * @dataProvider getValidStreets
     */
    public function testSetStreet($line1, $street, $streetNumber)
    {
        $address = new Address(
            array(
                'line1' => $line1,
            )
        );

        $this->assertSame($street, $address->street);
        $this->assertSame($streetNumber, $address->streetNumber);
    }

    /**
     * @dataProvider getValidStreets
     */
    public function testSetAndGetStreet($line1)
    {
        $address = new Address(
            array(
                'line1' => $line1,
            )
        );

        $this->assertSame($line1, $address->line1);
    }

    /**
     * @expectedException \DomainException
     */
    public function testSetInvalidStreet()
    {
        $address = new Address(
            array(
                'line1' => 'Invalid',
            )
        );
    }

    public function testSetAndGetAdditionalAddressLine()
    {
        $address = new Address(
            array(
                'line2' => 'Some random data',
            )
        );

        $this->assertSame('Some random data', $address->line2);
        $this->assertSame('Some random data', $address->additionalAddressLine);
    }
}
