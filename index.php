<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);

// 定义应用目录
define('APP_PATH','./Apps/');
define('__PUBLIC__','./Public/');

$scriptName = $_SERVER['SCRIPT_NAME']; // <-- "/foo/index.php"
$requestUri = $_SERVER['REQUEST_URI'];// <-- "/foo/bar?test=abc" or "/foo/index.php/bar?test=abc"
// Physical path
if (strpos($requestUri, $scriptName) !== false) {
    $physicalPath = $scriptName; // <-- Without rewriting
} else {
    $physicalPath = str_replace('\\', '', dirname($scriptName)); // <-- With rewriting
}
$script_name = rtrim($physicalPath, '/'); // <-- Remove trailing slashes

define("BASE_URL", $script_name . '/');
// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单