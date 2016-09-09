<?php
return array(
    'SESSION_EXPIRE'    => 3600,
	'DEFAULT_MODULE' =>'Home',
    'URL_MODEL' => 2,
    'SESSION_AUTO_START' => true,
    'LOAD_EXT_CONFIG' => 'user,db',
    'LOAD_EXT_FILE' => 'iconv.func',
    'DEFAULT_FILTER'        =>  'htmlspecialchars',
    'ALLOW_TRY_ERROR_TIMES' =>5,
    'APP_SUB_DOMAIN_DEPLOY'   =>    1, // 开启子域名或者IP配置
    'APP_SUB_DOMAIN_RULES'    =>    array(
        'mangguo.bbs.com' => 'Home',
    ),
);