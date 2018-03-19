<?php
namespace Wx;

class WxPay
{
    private $appid;
    
    private $mch_id;
    
    private $config;
    
    public function set_appid(string $appid)
    {
        $this->appid = $appid;
    }
    
    public function set_mch_id(string $mch_id)
    {
        $this->mch_id = $mch_id;
    }
    
    public function get_appid()
    {
        return $this->appid;
    }
    
    public function get_mch_id()
    {
        return $this->mch_id;
    }
    
    public function get_nonce_str()
    {
        return md5(uniqid());
    }
    
    public function __construct(array $config) {
        $this->config = $config;
        $this->appid = $config['app_id'];
        $this->mch_id = $config['mch_id'];
    }
    
    public function get_config(): array
    {
        return $this->config;
    }
}