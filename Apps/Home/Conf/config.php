<?php
return array(
	//'配置项'=>'配置值'
    'SESSION_AUTO_START'    =>  true,    // 是否自动开启Session
    'DEFAULT_CONTROLLER'    => 'Index',
    'DEFAULT_ACTION'        => 'index',
    'URL_ROUTER_ON'         => true,
    'TOKEN_ON'              => true,
    'TOKEN_NAME'            => '__hash__',
    'TOKEN_TYPE'            => 'md5',
    'TOKEN_RESET'           => true,
    'HTML_CACHE_ON'         => FALSE, // 开启静态缓存
    'HTML_CACHE_TIME'       => 60,   // 全局静态缓存有效期（秒）
    'HTML_FILE_SUFFIX'      => '.shtml', // 设置静态缓存文件后缀
    'URL_MAP_RULES' => array(
        'index'=>'Index/index',
        'mserver'=>'Mserver/index',
        'msqure'=>'Msqure/index',
        'mshop'=>'Mshop/index',
        'attaches'=>'Other/index',
        'server/fee'=>'Mserver/fee',
        'server/express'=>'Mserver/express',
        'server/hkeeping'=>'Mserver/houseKeeping',
        'server/recycle'=>'Mserver/recycle',
        'server/design'=>'Mserver/design',
        'server/other'=>'Mserver/attaches',
    ),
);