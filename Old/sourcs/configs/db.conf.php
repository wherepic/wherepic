<?php
/**
数据库配置文件
**/
if(!defined("IN_JAING")){header('HTTP/1.1 404 Not Found');} 
$db['dbhost'] = '127.0.0.1:3306';
$db['dbuser'] = 'root';
$db['dbpw'] = '';
$db['dbcharset'] = 'utf8';
$db['pconnect'] = '0';
$db['database'] = 'jaing';
$db['tablepre'] = 'c_';
return $db;
?>