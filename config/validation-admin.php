<?php
return [

    "admin/validPayment" => [
        "details" => [
            "total_payment" => "required|numeric|min:0",
            "amount" => "required|numeric|min:0",
            ],
        "invalid" => [
            "refuseReason" => "required|min:10",
        ],
    ],

    "messenger" => [
        "send" => [
            "expeditor" => "required",
            "method" => "required",
            "message" => "required|min:10",
        ],
    ],

];
