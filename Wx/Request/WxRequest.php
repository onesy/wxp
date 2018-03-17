<?php

namespace Wx\Request;

abstract class WxRequest {
    
    protected $wxpay;
    
    public function __construct(\Wx\WxPay $wxpay) {
        $this->appid = $wxpay->get_appid();
        $this->mch_id = $wxpay->get_mch_id();
        $this->nonce_str = $wxpay->get_nonce_str();
        $this->wxpay = $wxpay;
    }

    /**
     * 生成签名
     * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    public function makeSign($data) {
        // 去空
        $data = array_filter($data);
        //签名步骤一：按字典序排序参数
        ksort($data);
        $string_a = http_build_query($data);
        $string_a = urldecode($string_a);
        //签名步骤二：在string后加入KEY
        $config = $this->wxpay->get_config();
        $string_sign_temp = $string_a . "&key=" . $config['key'];
        //签名步骤三：MD5加密
        $sign = md5($string_sign_temp);
        // 签名步骤四：所有字符转为大写
        $result = strtoupper($sign);
        return $result;
    }
    
    public function __set($name, $value) {
        $this->params[$name] = $value;
    }
    
    public function __get($name) {
        return $this->params[$name];
    }
    
    public function check_params():bool
    {
        foreach($this->require as $require) {
            if (empty($this->params[$require]))
            {
                throw new WxParamException("params:{$require} require!");
            }
        }
        return true;
    }
    
    public function xml_generator():string 
    {
        $this->check_params();
        $xml = "<xml>";
        foreach($this->params as $key => $value)
        {
            $xml .= "<{$key}>{$value}</{$key}>";
        }
        $xml .= "</xml>";
        return $xml;
    }
    
    public function request()
    {
        $xml = $this->xml_generator();
        $url = $this->url;
    }

}
