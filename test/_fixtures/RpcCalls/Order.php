<?php
return new \Bepado\SDK\Struct\RpcCall(
    array(
        "service" => "ProductService",
        "command" => "testOrder",
        "arguments" => array(
            new \Bepado\SDK\Struct\Order(array(
                'shipping' => new \Bepado\SDK\Struct\Shipping(array(
                    'shippingCosts' => 5.,
                    'grossShippingCosts' => 6.,
                )),
            ))
        )
    )
);
