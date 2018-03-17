<?php
namespace Tests;

use Wx\Request\CreateBidRequest;
use Wx\WxPay;

class TestCreateBid {
    public function test($config)
    {
//        var_dump(strlen("otn" . date("Y-m-d H:i:s")));die;
        $wxpay = new WxPay($config);
        $create_bid_reqeust = new CreateBidRequest($wxpay);
        $create_bid_reqeust->body = "test mibi";
        $create_bid_reqeust->out_trade_no = "otn" . date("YmdHis");
        $create_bid_reqeust->total_fee = 1;
        $create_bid_reqeust->spbill_create_ip = "123.12.12.123";
        $create_bid_reqeust->time_expire = date("YmdHis", strtotime("+ 3600 second"));
        $create_bid_reqeust->time_start = date("YmdHis");
        $create_bid_reqeust->trade_type = 'APP';
        $create_bid_reqeust->scene_info = json_encode(['name' => 'guimi app', 'version' => 2.0]);
        $create_bid_reqeust->request();
        var_dump($create_bid_reqeust->is_job_success());
    }
    
    public function test_guzzle($path)
    {
        $xml = file_get_contents($path);
        $xml_node = simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA);
        echo $xml_node->return_code[0];
    }
}
