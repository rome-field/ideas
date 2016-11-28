<?php

namespace App\Http\Controllers\Front;


use Log;
use App\WechatApi;
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
            Log::error("Wechat TOKEN id not defined!");
        }

        $tmpArr = array($token, $_GET["timestamp"], $_GET["nonce"]);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);

        return sha1($tmpStr) == $_GET["signature"];
    }

    public function create_menu(WechatApi $wechat)
    {
        $menu_data =  {
            "button":[
                {
                    "type":"view",
                    "name":"我想说",
                    "url":"www.sogou.com",
                },
                {
                    'type':'view',
                    'name':'大家说',
                    'url':"www.baidu.com",
                },
            ]
        };

        $res = $wechat->createMenu($menu_data);

        if($res == 0){
            return "success";
        } else {
            return "failed. error code: ".$res;
        }
    }
}