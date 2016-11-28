<?php

namespace App;


use Cache;
use Log;

class WechatApi
{
    // get access_token
    public function getAccessToken()
    {
        $access_token = Cache::get('access_token');
        if(!$access_token){
            $access_token = $this->_getAccessToken();
        }
        return $access_token;
    }

    public function createMenu($fields)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->getAccessToken();
        $res = $this->curl_opt($url, "post", $fields);

        if(!$res){
            return Log::error('create menu error because connection error');;
        }

        if($res['errcode'] == 0){
            Log::log('create menu success');;
        }
        return $res['errcode'];
    }

    public function getMenu()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=".$this->getAccessToken();
        $res = $this->curl_opt($url);

        if(!$res){
            return Log::error('get menu error because connection error');;
        }
        Log::log('get menu success');
        return $res;
    }

    public function deleteMenu()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$this->getAccessToken();
        $res = $this->curl_opt($url);

        if(!$res){
            return Log::error('delete menu error because connection error');;
        }

        if($res['errcode'] == 0){
            Log::log('delete menu success');;
        }
        return $res['errcode'];
    }

    //resolve access_token url, get access_token
    private function _getAccessToken()
    {
        $app_id = config("wechat.APP_ID");
        $app_secret = config("wechat.APP_SECRET");
        if(!$app_id && !$app_secret) {
            Log::error("APP_ID and APP_SECRET not found");
        }

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$app_id&secret=$app_secret";
        $res = $this->curl_opt($url, "access_token");

        if(!$res)
        {
            return Log::error("didn't get access_token because connection error!");
        }

        if(isset($res['access_token']))
        {
            Cache::put('access_token', $res['access_token'], (int)($res['expires_in']-60)/60);
            return $res['access_token'];
        }else {
            return Log::error("didn't get wechat access_token:".$res['errmsg']);
        }
    }

    private function curl_opt($url, $opt="get", $fields=null, $data_type="json")
    {
        $curl = curl_init();
        curl_setopt ($curl, CURLOPT_URL, $url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        if($opt == 'post'){
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
        }

        $res=curl_exec($curl);
        $status = curl_getinfo($curl);
        curl_close($curl);
        if (isset($status['http_code']) && $status['http_code'] == 200)
        {
            if($data_type == "json"){
                return json_decode($res, true);
            }
            return $res;
        } else {
            Log::error('curl get errror. errror code: '.$status['http_code']);
            return false;
        }
    }

}
