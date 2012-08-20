<?php

/**
|---------------------------------------------------------------
| 文件缓存处理工厂
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------
*/

Class CCache{
    private $cache = null;
    public function __construct(){
        $this->cache = $this->factory();
    }    
    
    public function factory(){
        $Type = parse_name(C('DATA_CACHE_TYPE'));
        $ClassName = 'C'.$Type;
        $ClassFIle = __CSOURCS__.'Core/Driver/Cache/'.$ClassName.'.class.php';
        if(!is_file($ClassFIle)){
            CLog::write(str_replace('%s%', $Type, L("ERR_CACHE_CLASS_FILE")));    
        }
        include_once($ClassFIle);
        return new $ClassName();
    }

    public function set($name,$value,$path = '') {
        return $this->cache->set($name,$value,$path);
    }

    public function get($name,$path = '') {
        return $this->cache->get($name,$path);
    }
}

?>