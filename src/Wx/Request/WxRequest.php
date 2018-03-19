<?php

namespace Wx\Request;

use GuzzleHttp\Client;
use Wx\Exceptions\WxParamException;
abstract class WxRequest {
    
    protected $key_required = false;

    protected $wxpay;
    
    public $base_url = "https://api.mch.weixin.qq.com";
    
    public $timeout = 5;
    
    public $outfile = "/tmp/guzzle.out";
    
    public $response_body = null;
    
    public $verify_sign = true;
    
    public function __construct(\Wx\WxPay $wxpay) {
        $this->appid = $wxpay->get_appid();
        $this->mch_id = $wxpay->get_mch_id();
        $this->nonce_str = $wxpay->get_nonce_str();
        $this->notify_url = $wxpay->get_config()['notify_url'];
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
    
    public function xmlobject2array(\SimpleXMLElement $xmlobject)
    {
        libxml_disable_entity_loader(true);
        $result = json_decode(json_encode($xmlobject), true);
        return $result;
    }
    
    public function check_sign():bool
    {
        if ($this->verify_sign == false) {
            return true;
        }
        $rtn = $this->xmlobject2array($this->response_body);
//        var_dump($rtn);die;
        $sign = $rtn['sign'];
        unset($rtn['sign']);
        ksort($rtn);
        if ($sign == $this->makeSign($rtn))return true;
        return false;
    }
    
    public function request()
    {
        $this->sign = $this->makeSign($this->params);
        $xml = $this->xml_generator();
//        var_dump($xml);die;
        $base_url = $this->base_url;
        $client = new Client([
            'base_uri' => $base_url,
            'timeout' => $this->timeout,
        ]);
        $response = $client->request("POST", $this->uri, [
            'body' => $xml,
            'debug' => true,
            'headers' => ["Content-type: text/xml"],
            'sink' => $this->outfile
        ]);
        if ($response->getStatusCode() !== 200)
        {
            throw new \Exception("request faild");
        }
        $return_info = $response->getBody();
        
        $this->response_body = simplexml_load_string($return_info,'SimpleXMLElement',LIBXML_NOCDATA);
        if (!$this->check_sign())
        {
            throw new Exception("return sign check Error");
        }
//        var_dump($this->check_sign());
        return $this->response_body;
    }
    
    public function is_job_success()
    {
        if ((string)$this->response_body->result_code == "SUCCESS")
        {
            return true;
        }
        return false;
    }

}
