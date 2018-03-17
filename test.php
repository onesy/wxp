<?php

include "vendor/autoload.php";
include("Tests/TestCreateBid.php");
include("Wx/WxPay.php");
include("Wx/Exceptions/WxParamException.php");
include("Wx/Request/WxRequest.php");
include("Wx/Request/CreateBidRequest.php");
$config = include("Tests/config.php");
$test_create_bid = new Tests\TestCreateBid();
$test_create_bid->test($config['wx']);
//$test_create_bid->test_guzzle("/home/onesy/tmp/guzzle.out");