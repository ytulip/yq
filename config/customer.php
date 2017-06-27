<?php

return [
    'wechat'=>[
        'appid'=>env('WECHAT_APPID'),
        'appsercret'=>env('WECHAT_APPSERCRET'),
        'mechid'=>env('WECHAT_MECHID'),
        'key'=>env('WECHAT_KEY'),
        'token'=>env('WECHAT_TOKEN'),
        'token_path'=>storage_path() . '/wechat/access_token.json',
        'ticket_path'=>storage_path() . '/wechat/jsapi_ticket.json',
        'menu_path'=>storage_path() . '/wechat/menu.json' //自定义菜单存储路径
    ],
];
