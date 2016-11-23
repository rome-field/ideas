<?php

namespace App\Http\Controllers\Front;


use App\Http\Controllers;

class WechatApiController extends Controllers
{
    public function valid()
    {
        $echoStr = $_GET['echostr'];

        //valid signature, option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
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

        if($tmpStr = $signature){
            return true;
        }else{
            return false;
        }

    }
}