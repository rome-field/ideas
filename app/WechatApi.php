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
            $access_token = $this->resoloveToken();
        }
        return $access_token;
    }

    //resolve access_token url, get access_token
    private function resoloveToken()
    {
        $app_id = config("wechat.APP_ID");
        $app_secret = config("wechat.APP_SECRET");
        if(!$app_id && !$app_secret) {
            Log::error("APP_ID and APP_SECRET not found");
        }

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$app_id&secret=$app_secret";
        $res = $this->curlWechatApi($url, "access_token");
        if(isset($res['access_token']))
        {
            Cache::put('access_token', $res['access_token'], (int)($res['expires_in']-60)/60);
            return $res['access_token'];
        }else {
            Log::error("didn't get wechat access_token:".$res['errmsg']);
        }
    }

    private function curlWechatApi($url, $opt, mixed $data=null)
    {
        $curl = curl_init();
        curl_setopt ($curl, CURLOPT_URL, $url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        $res=curl_exec($curl);
        curl_close($curl);
        return json_decode($res, true);
    }

}