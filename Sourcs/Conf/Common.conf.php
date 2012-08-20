<?php

/**
|---------------------------------------------------------------
| 配置文件
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------
*/

if(!defined('IN_CPHP')) {exit();}

return  array(

    /* 默认设定 */
    'DEFAUL_C_LANG'         => 'zh_cn', // 默认语言
    'DEFAUL_C_MODULE'       => '', // 默认模块名称
    'DEFAUL_C_CONTROL'      => 'Index', // 默认控制器名称
    'DEFAUL_C_ACTION'       => 'init', // 默认操作名称
    'DEFAUL_C_CHARSET'      => 'utf-8', // 默认输出编码
    'DEFAUL_C_AJAX_RETURN'  => 'JSON',  // 默认AJAX 数据返回格式,可选JSON XML ...
    'DEFAUL_HASH_KEY'       => 'caojiayin1984@gmail.com',
    'DEFAUL_URL_REWRITE'    => true, //URL地址重写
    'DEFAUL_TPL_FORMAT'     => true, //生成模板压缩
    'DEFAUL_HALT_TRACE'     => true, //trace调试输出
    
 
    /* Cookie设置 */
    'COOKIE_EXPIRE'         => 3600,    // Coodie有效期
    'COOKIE_DOMAIN'         => '',      // Cookie有效域名
    'COOKIE_PATH'           => '/',     // Cookie路径
    'COOKIE_PREFIX'         => '',      // Cookie前缀 避免冲突	

    /* 数据缓存设置 */
    'DATA_CACHE_TIME'       => 0,      // 数据缓存有效期 0表示永久缓存
    'DATA_CACHE_COMPRESS'   => true,   // 数据缓存是否压缩缓存
    'DATA_CACHE_CHECK'      => true,   // 数据缓存是否校验缓存
    'DATA_CACHE_TYPE'       => 'File',  // 数据缓存类型,支持:File|Db|Memcache
    'DATA_CACHE_PATH'       => __CCACHE__.'Data/',// 缓存路径设置 (仅对File方式缓存有效)
    'DATA_MEMCACHE_HOST'    => '',
    'DATA_MEMCACHE_PORT'    => '',
    'DATA_MEMCACHE_TIMEOUT' => '',

    /* 模板引擎设置 */
    'TPL_SCRIPT'            => 'Script', //JS目录名
    'TPL_IMAGE'             => 'Images', //图片目录名
    'TPL_STYLE'             => 'Style', //样式目录名    
    'TPL_FILE_STYLE'        => 'Default/', //模板皮肤
    'TPL_FILE_DIR'          => __CTPL__, //模板路径
    'TPL_FILE_CACHE'        => __CCACHE__.'Template/', //模板缓存路径	
    'TPL_FILE_HTML'         => __CCACHE__.'Html/', //模板静态输出路径
    'TPL_SUFFIX'            => '.htm',  // 默认模板文件后缀
    'TPL_CACHE_SUFFIX'      => '.tpl.php',  // 默认缓存模板文件后缀
    'TPL_STATIC_TIME'       => 0, //静态输出有郊期
    'TPL_DETECT_STATIC'     => false, //默认静态输出
    'TPL_OUT_STATIC'        => false, //默认静态输出
    'TPL_PUBLIC'            => 'Public/', //公共模板位置
    'TPL_PUBLIC_STYLE'      => 'Public/Style/', //公共样式路径
    'TPL_ERROR_FILE'        => 'Error',  //错误输出模板
    'ERROR_PAGE'            => 'Error',  //出错跳转页面

    /* 日志设置 */
    'LOG_RECORD'            => true,   // 记录日志
    'LOG_TYPE'              => 3, // 日志记录类型 0 系统 3 文件 默认为文件方式
    'LOG_FILE_SIZE'         => 2097152, // 日志文件大小限制
    
    /* 上传设置 */
    'UP_VER_TYPE'           => 'sign', //传文件类型验证方式（sign：签名(安全)，ext：扩展名）
    'UP_SAVE_TEMP'          => false, //启用上传文件临时存放
    'UP_TEMP_ROOT'          =>  __CUPLOAD__.'Temp/', //临时存放目录
    'UP_MAXSIZE'            =>  2 ,//文件上传大小(MB)
    'UP_RENAME'             => true, //是否重命名
    'UP_FILE_EXT'           =>  'jpg,gif,png,bmp,rar,zip,doc,ppt,xls,swf,docx', //可上传文件类型
    
    /* 数据库设置 */
    'DB_TYPE'               => 'mysqli',     // 数据库类型
    'DB_HOST'               => '127.0.0.1', // 服务器地址
    'DB_NAME'               => 'jaing',          // 数据库名
    'DB_USER'               => 'root',      // 用户名
    'DB_PWD'                => '',          // 密码
    'DB_PORT'               => '3306',        // 端口
    'DB_PREFIX'             => 'c_',    // 数据库表前缀
    'DB_CONNENT'            => true,    // 是否是长链接
    'DB_CHARSET'            => 'utf8',    // 是否是长链接


);
?>