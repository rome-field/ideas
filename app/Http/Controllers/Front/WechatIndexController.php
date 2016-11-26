<?php

namespace App\Http\Controllers\Front;


use Log;
use App\Http\Controllers\Controller;

class WechatIndexController extends Controller
{
    // check wechat is valid
    public function valid()
    {
        $echoStr = $_GET['echostr'];

        //valid signature, option
        if($this->checkSignature()){
            Log::info("pass wechat first check!");
            return $echoStr;
        }
    }

    private function checkSignature()
    {
        $token = config('wechat.TOKEN', false);

        if(!$token){
            throw new Exception();
            Log::error("Wechat TOKEN id not defined!");
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