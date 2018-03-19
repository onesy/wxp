<?php
namespace Wx\Request;
use Wx\Exceptions\WxParamException;

class CreateBidRequest extends WxRequest
{
    public $uri = "/pay/unifiedorder";
    
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
    
    protected $params = [
        'device_info' => 'WEB',
        'sign_type' => 'MD5',
        'trade_type' => 'APP',
    ];
    
    public function __construct(\Wx\WxPay $wxpay) {
        $this->key_required = true;
        parent::__construct($wxpay);
    }
    
    public function get_token():string
    {
        return (string)$this->body->prepay_id;
    }
}