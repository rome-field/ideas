<?php

namespace App\Http\Controllers\Front;


use Cache;
use App\Http\Controllers\Controller;

class WechatApiController extends Controller
{
    // check wechat is valid
    public function valid()
    {
        $echoStr = $_GET['echostr'];

        //valid signature, option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

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
        $app_id = config("constants.APP_ID");
        $app_secret = config("constants.APP_SECRET");
        if(!$app_id && !$app_secret) {
            throw new Exception("APP_ID and APP_SECRET not found", 1);
        }

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$app_id&secret=$app_secret";
        $res = json_decode(file_get_contents($url), true);
        if(isset($res['access_token']))
        {
            Cache::put('access_token', $res['access_token'], (int)($res['expires_in']-60)/60);
            return $res['access_token'];
        }else {
            throw new Exception("didn't get wechat access_token:".$res['errmsg'], 1);
        }
    }

    private function checkSignature()
    {
        $token = config('constants.TOKEN', false);

        if(!$token){
            throw new Exception("Wechat TOKEN id not defined!");
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $tmpArr = array($token, $timestamp, $nonce);

        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if($tmpStr == $signature){
            return true;
        }else{
            return false;
        }

    }
}