<?php
namespace Wx\Request;
use Wx\Exceptions\WxParamException;

class CreateBidRequest extends WxRequest
{
    public $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
    
    public $require = [
        'appid',
        'mch_id',
        'nonce_str',
        'sign',
        'body',
        'out_trade_no',
        'total_fee',
        'spbill_create_ip',
        'notify_url',
        'trade_type',
    ];
    
    private $params = [
        'device_info' => 'WEB',
        'sign_type' => 'MD5',
        'trade_type' => 'APP',
    ];
    
    public function __construct(\Wx\WxPay $wxpay) {
        parent::__construct($wxpay);
    }
}