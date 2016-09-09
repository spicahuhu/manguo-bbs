<?php
return array(
	//'配置项'=>'配置值'
    'SESSION_AUTO_START'    =>  true,    // 是否自动开启Session
    'TMPL_ACTION_ERROR'     =>  'Public:success', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   =>  'Public:success', // 默认成功跳转对应的模板文件
    'ALLOW_TRY_ERROR_TIMES' => 5,
    'DEFAULT_CONTROLLER'    => 'Public',
    'DEFAULT_ACTION'        => 'login',
    'URL_ROUTER_ON'         => true,
    'TOKEN_ON'              => true,
    'TOKEN_NAME'            => '__hash__',
    'TOKEN_TYPE'        => 'md5',
    'TOKEN_RESET'       => true,
    'URL_MAP_RULES' => array(
        'login' => 'Public/login',
        'loginOut' => 'Public/loginOut',
        'setPwd' =>'Index/changePwd',
        'editMain'=>'Index/editMain',
        'estars'=>'Family/editStars',
        'index'=>'Index/index',
        'stars'=>'Family/stars',
        'ins' =>'FamilyIncome/income',
        'insExport'=>'FamilyIncome/export',
        'cas'=>'Family/cashout',
        'casExport'=>'Family/cashoutExport',
    ),
);