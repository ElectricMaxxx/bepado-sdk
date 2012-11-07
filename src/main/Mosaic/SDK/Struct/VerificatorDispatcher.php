<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\Struct;

use Mosaic\SDK\Struct;

/**
 * Visitor verifying integrity of struct classes
 *
 * @version $Revision$
 */
class VerificatorDispatcher
{
    /**
     * Registered verificators
     *
     * @var array
     */
    protected $verificators = array();

    public function __construct(array $verificators = array())
    {
        foreach ($this->verificators as $class => $verificator) {
            $this->addVerificator($class, $verificator);
        }
    }

    /**
     * Add verificator
     *
     * @param string $class
     * @param Verificator $verificator
     * @return void
     */
    public function addVerificator($class, Verificator $verificator)
    {
        $this->verificators[$class] = $verificator;
    }

    /**
     * Method to verify a structs integrity
     *
     * Throws a RuntimeException if the struct does not verify.
     *
     * @param Struct $struct
     * @return void
     */
    public function verify(Struct $struct)
    {
        if (!isset($this->verificators[get_class($struct)])) {
            throw new \OutOfBoundsException(
                "No verificator available for class " . get_class($struct)
            );
        }

        $this->verificator[get_class($struct)]->verify($this, $struct);
    }
}
