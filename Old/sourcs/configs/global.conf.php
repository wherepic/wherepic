<?php
if(!defined("IN_JAING")){header('HTTP/1.1 404 Not Found');} 
// ----------------------------  CONFIG domain  ----------------------------- //
$_config['domain']  =   'http://www.yea.com/';
$_config['isRewrite'] = true; //开启伪静态
// ----------------------------  CONFIG COOKIE  ----------------------------- //
$_config['cookie']['cookiepre'] = 'FCrJ_';
$_config['cookie']['cookiedomain'] = '';
$_config['cookie']['cookiepath'] = '/';
$_config['cookie']['cookiepre'] = 'XhM_';
// ----------------------------  CONFIG CACHE  ------------------------------ //
$_config['cache']['mode'] = 'file';
$_config['cache']['file']['data'] = 'data/cache/';
$_config['cache']['memcache']['hostname'] = '';
$_config['cache']['memcache']['port'] = '';
$_config['cache']['memcache']['timeout'] = '';
$_config['cache']['memcache']['debug'] = '';
// ----------------------------  CONFIG TEMPLATE  --------------------------- //
$_config['template']['tpldir'] = 'template/'; //模板位置
$_config['template']['tplcache'] = 'data/template/'; //缓存位置
$_config['template']['html'] = 'data/html/'; //缓存位置
$_config['template']['static'] = false; //开启静态缓存
$_config['template']['skin'] = 'default'; //开启静态缓存
// ----------------------------  CONFIG UPLOAD  ----------------------------- //
$_config['upload']['temp']  =   false; //临时存放开启
$_config['upload']['size']  =   '4096'; //KB
$_config['upload']['root']  =   'attachments/';
$_config['upload']['ext']  =   array('gif','jpg','png'); //上传文件类型
// ----------------------------  CONFIG HASH  ------------------------------- //
$_config['hash']['key'] = 'CaoJiaYin1984@126.com';
$_config['language'] = 'zh_cn'; //语言,中文:zh_cn,繁体zh_tw,英文：en

return $_config;
?>