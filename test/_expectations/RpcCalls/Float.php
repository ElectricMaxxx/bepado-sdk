<?php
return new \Bepado\SDK\Struct\RpcCall(
    array(
        "service" => "FloatService",
        "command" => "testFloat",
        "arguments" => array(
            42.3
        )
    )
);
