<?php
return new \Bepado\SDK\Struct\RpcCall(
    array(
        "service" => "ArrayService",
        "command" => "testArray",
        "arguments" => array(
            array(
                "foo",
                "bar",
                23,
                42.3,
                true,
                false,
                null,
                array()
            )
        )
    )
);
