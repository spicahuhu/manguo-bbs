<?php
return array(
	//'配置项'=>'配置值'
    'SESSION_AUTO_START'    =>  true,    // 是否自动开启Session
    'DEFAULT_CONTROLLER'    => 'Index',
    'DEFAULT_ACTION'        => 'index',
    'URL_ROUTER_ON'         => true,
    'TOKEN_ON'              => true,
    'TOKEN_NAME'            => '__hash__',
    'TOKEN_TYPE'        => 'md5',
    'TOKEN_RESET'       => true,
    'URL_MAP_RULES' => array(
        'index'=>'Index/index',
        'mserver'=>'Mserver/index',
        'msqure'=>'Msqure/index',
        'mshop'=>'Mshop/index',
        'attaches'=>'Other/index',
    ),
);