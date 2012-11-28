<?php
/**
 * This file is part of the Mosaic SDK Component.
 *
 * @version $Revision$
 */

namespace Mosaic\SDK\ChangeVisitor;

use Mosaic\SDK\ChangeVisitor;
use Mosaic\SDK\Struct;

/**
 * Visits intershop changes ito messages
 *
 * @version $Revision$
 */
class Message extends ChangeVisitor
{
    /**
     * Visit changes
     *
     * @param array $changes
     * @return array
     */
    public function visit(array $changes)
    {
        $messages = array();
        foreach ($changes as $shop => $shopChanges) {
            foreach ($shopChanges as $change) {
                switch (true) {
                    case $change instanceof Struct\Change\InterShop\Update:
                        $messages = array_merge(
                            $messages,
                            $this->visitUpdate($change)
                        );
                    break;
                    case $change instanceof Struct\Change\InterShop\Delete:
                        $messages = array_merge(
                            $messages,
                            $this->visitDelete($change)
                        );
                    break;
                    default:
                        throw new \RuntimeException(
                            'No visitor found for ' . get_class($change)
                        );
                }
            }
        }

        return $messages;
    }

    /**
     * Visit update change
     *
     * @param Struct\Change\InterShop\Update $change
     * @return void
     */
    protected function visitUpdate(Struct\Change\InterShop\Update $change)
    {
        $messages = array();

        if ($change->product->availability !== $change->oldProduct->availability) {
            $messages[] = new Struct\Message(
                array(
                    'message' => 'Availability of product %product changed to %availability.',
                    'values' => array(
                        'product' => $change->product->title,
                        'availability' => $change->product->availability,
                    ),
                )
            );
        }

        if ($change->product->price !== $change->oldProduct->price) {
            $messages[] = new Struct\Message(
                array(
                    'message' => 'Price of product %product changed to %price.',
                    'values' => array(
                        'product' => $change->product->title,
                        'price' => $change->product->price,
                    ),
                )
            );
        }

        return $messages;
    }

    /**
     * Visit delete change
     *
     * @param Struct\Change\InterShop\Delete $change
     * @return void
     */
    protected function visitDelete(Struct\Change\InterShop\Delete $change)
    {

    }
}
