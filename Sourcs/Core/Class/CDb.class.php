<?php

/**
|---------------------------------------------------------------
| 数据工厂
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------
*/

Class CDb{

    protected $dbType           = null; //数据库类型	
    protected $linkID           =   null; //当前连接ID
    protected $Db                 = null; //数据库操作对象
    
    public function __construct(){
        $this->Db = $this->factory();
    }

    /**
     |---------------------------------------------------------
     | 加载数据库处理类并实例化
     |----------------------------------------------------------
     | @param mixed $config 数据库配置信息
     |----------------------------------------------------------
     | @return Object
     |----------------------------------------------------------
     */
    public function factory() {
    	$Config = $this->getConfig();
    	if(empty($Config['TYPE'])){
    		CLog::write(L("ERR_DB_CONFIG_TYPE"));
    	}
    	$this->dbType = parse_name($Config['TYPE']);
        $Class = 'Db'.$this->dbType; 
    	$DbClassFIle = __CSOURCS__.'Core/Driver/Db/'.$Class.'.class.php';
    	if(!is_file($DbClassFIle)){
    		CLog::write(str_replace('%s%', $this->dbType, L("ERR_DB_CLASS_FILE")));	
    	}
    	include_once($DbClassFIle);
    	return new $Class($Config);
    } 

    /**
     |----------------------------------------------------------
     | 数据库配置信息
     |----------------------------------------------------------
     | @param mixed $config 数据库配置信息
     |----------------------------------------------------------
     | @return Array
     |----------------------------------------------------------
     */
    private function getConfig() {
        return array (
            'TYPE'        =>   C('DB_TYPE'),
            'USERNAME'  =>   C('DB_USER'),
            'PASSWORD'   =>   C('DB_PWD'),
            'HOST'  =>   C('DB_HOST'),
            'PORT'    =>   C('DB_PORT'),
            'DATABASE'   =>   C('DB_NAME'),
            'CONNENT'   =>   C('DB_CONNENT'),
            'CHARSET'   =>   C('DB_CHARSET'),
        );
    }
    
    /**
     |----------------------------------------------------------
     | 查询
     |----------------------------------------------------------
     | @param mixed $TableName  表名
     | @param mixed $Params  组合语句
     | @param mixed $Key  返回数组KEY值
     |----------------------------------------------------------
     | @return Array
     |----------------------------------------------------------
     */     
    public function DbSelect($TableName = '', $Params = array(), $Key = ''){
        $Table = $this->parseTable($TableName);
        $Distinct = $this->parseDistinct($Params['distinct']);
        $Having = $this->parseHaving($Params['having']);
        if($Params['fields']){
           if(is_array($Params['fields'])){
                $field = explode(',',$Params['field']);
                array_walk($field, array($this, 'parseKey'));           
           }elseif(is_string($Params['fields'])){
                $field = $Params['fields'];
           }

        }else{
            $field = '*';
        }
        $Order = $this->parseOrder($Params['orders']);
        $Join = $this->parseJoin($Params['join']);
        $Group = $this->parseGroup($Params['group']);
        $Limit = $this->parseLimit($Params['limit'],$Params['page']);
        $Condition = $this->parseCondition($Params['conditions']);
        $sql = 'SELECT '.$Distinct.$field.' FROM '.$Table.$Join.$Condition.$Order.$Group.$Having.$Limit.'';
        return $this->Db->FetchArray($sql,$Key);
    }

    /**
     |----------------------------------------------------------
     | 写入数据
     |----------------------------------------------------------
     | @param mixed $TableName  表名
     | @param mixed $Data  组合语句
     | @param mixed $Replace  是否Replace
     | @param mixed $Lock  是否锁定  针对innob
     |----------------------------------------------------------
     | @return INT
     |----------------------------------------------------------
     */ 
    public function DbInsert($TableName, $Data, $Replace=false,$Lock = false){
        if(!$Data || !is_array($Data)){return false;}
        $values  =  $fields    = array();
        foreach ($Data as $key => $val) {
            $fields[] = $this->parseKey($key);
            $values[] = $this->parseValue($val); 
        }
        $sql = ($Replace?'REPLACE':'INSERT').' INTO '.$this->parseTable($TableName).' ('.implode(',', $fields).') VALUES ('.implode(',', $values).')';
        $sql   .= $this->parseLock($Lock);
        $this->Db->Query($sql);
        return $this->Db->InsertID();
    }

    /**
     |----------------------------------------------------------
     | 更新记录
     |---------------------------------------------------------- 
     | @param mixed $data 数据
     | @param array $options 表达式
     |----------------------------------------------------------
     | @return INT
     |----------------------------------------------------------
     */
    public function DbUpdate($TableName,$Params) {  
        $Condition = $this->parseCondition($Params['conditions']);
        $Order = $this->parseOrder($Params['orders']); 
        $Limit = $this->parseLimit($Params['limit']);        
        $sql   = 'UPDATE '.$this->parseTable($TableName).$this->parseSet($Params['fields']).$Condition.$Order.$Limit;
        $this->Db->Query($sql);
        return $this->Db->AffectedRows();
    } 
    
    /**
     |----------------------------------------------------------
     | 删除记录
     |---------------------------------------------------------- 
     | @param mixed $data 数据
     | @param array $options 表达式
     |----------------------------------------------------------
     | @return INT
     |----------------------------------------------------------
     */
    public function DbDelete($TableName,$Params,$Lock = false) {  
        $Condition = $this->parseCondition($Params['conditions']);
        $Order = $this->parseOrder($Params['orders']); 
        $Limit = $this->parseLimit($Params['limit']);        
        $sql   = 'DELETE FROM '.$this->parseTable($TableName).$Condition.$Order.$Limit.$this->parseLock($Lock);
        $this->Db->Query($sql);
        return $this->Db->AffectedRows();
    }
     
    /**
     |----------------------------------------------------------
     | 查询记录，获取一行数据
     |---------------------------------------------------------- 
     | @param mixed $TableName  表名
     | @param mixed $Params  组合语句
     |----------------------------------------------------------
     | @return Array
     |----------------------------------------------------------
     */
    public function DbgetRow($TableName,$Params){
        $Table = $this->parseTable($TableName);
        $Distinct = $this->parseDistinct($Params['distinct']);
        $Having = $this->parseHaving($Params['having']);
        if($Params['fields']){
           if(is_array($Params['fields'])){
                $field = explode(',',$Params['field']);
                array_walk($field, array($this, 'parseKey'));           
           }elseif(is_string($Params['fields'])){
                $field = $Params['fields'];
           }

        }else{
            $field = '*';
        }
        $Order = $this->parseOrder($Params['orders']);
        $Join = $this->parseJoin($Params['join']);
        $Group = $this->parseGroup($Params['group']);
        $Limit = $this->parseLimit($Params['limit'],$Params['page']);
        $Condition = $this->parseCondition($Params['conditions']);
        $sql = 'SELECT '.$Distinct.$field.' FROM '.$Table.$Join.$Condition.$Order.$Group.$Having.$Limit.'';
        return $this->Db->ResultRow($sql);    
    }
    
    /**
     |----------------------------------------------------------
     | 查询记录，获取指定列数据
     |---------------------------------------------------------- 
     | @param mixed $TableName  表名
     | @param mixed $Params  组合语句
     |----------------------------------------------------------
     | @return
     |----------------------------------------------------------
     */
    public function DbgetOne($TableName,$Params){
        $Table = $this->parseTable($TableName);
        $Distinct = $this->parseDistinct($Params['distinct']);
        $Having = $this->parseHaving($Params['having']);
        if($Params['field']){
           if(is_array($Params['field'])){
                $field = explode(',',$Params['field']);
                array_walk($field, array($this, 'parseKey'));           
           }elseif(is_string($Params['field'])){
                $field = $Params['field'];
           }

        }else{
            $field = '*';
        }
        $Order = $this->parseOrder($Params['orders']);
        $Join = $this->parseJoin($Params['join']);
        $Group = $this->parseGroup($Params['group']);
        $Limit = $this->parseLimit($Params['limit'],$Params['page']);
        $Condition = $this->parseCondition($Params['conditions']);
        $sql = 'SELECT '.$Distinct.$field.' FROM '.$Table.$Join.$Condition.$Order.$Group.$Having.$Limit.'';
        return $this->Db->ResultColumn($sql);    
    }
           
    /**
     |----------------------------------------------------------
     | set分析                                                     
     |----------------------------------------------------------
     | @param array $data
     |----------------------------------------------------------
     | @return string
     |----------------------------------------------------------
     */
    public function parseSet($data) {
        if(empty($data)){
            halt(L("ERR_DB_EXPRESS_ERROR"));
        }
        foreach ($data as $key=>$val){
            $value   =  $this->parseValue($val);
            $set[]    = $this->parseKey($key).'='.$value;     
        }
        return ' SET '.implode(',',$set);
    }    
    /**
     |----------------------------------------------------------
     | 条件解析
     |----------------------------------------------------------
     | @param mixed $condition 
     |----------------------------------------------------------
     | @return string
     |----------------------------------------------------------
     */ 
    public function parseCondition($condition){
        $strCondition = '';
        if($condition){
            $strCondition = ' WHERE ';
            //如果是字符串，直接返回
            if(is_string($condition)) {
                return $strCondition.$condition;   
            }else{//数组分析
                $AND = array();
                $OR = array();
                foreach ($condition as $key=>$val){
                    // 查询字段的安全过滤
                    if(!preg_match('/^[A-Z_\|\>\<\!\=\-.a-z0-9\(\)\, ]+$/',trim($key))){
                        CLog::write(L('ERR_DB_EXPRESS_ERROR').':'.$key);
                    } 
                    if(strtolower($key) == 'or'){
                        foreach($val As $k => $v){
                            if(is_array($v)){
                                $temp = array();
                                foreach($v as $k1 => $v1){
                                    $temp[] = $this->parseSign($k1,$v1);
                                }
                                $OR[] = ' ( '. implode(" AND ",$temp) .' ) ';
                            }else{
                                $OR[] = $this->parseSign($k,$v);
                            }
                        }    
                    }else{
                        $AND[] = $this->parseSign($key,$val);
                        //$strCondition    
                    }                   
                }
            }
            $strCondition .= implode(" AND ",$AND).' '.($OR ? ' AND ('.implode(" OR ",$OR).') ' : ' ');
        }    
        return $strCondition; 
    }

    /**
     |----------------------------------------------------------
     | 操作符
     |----------------------------------------------------------
     | @param mixed $key 
     | @param mixed $val 
     |----------------------------------------------------------
     | @return string
     |----------------------------------------------------------
     */     
    public function parseSign($key,$val){
        $result = '';
        if( substr($key,-1) == '>'        ||    substr($key,-1) == '<'        ||
            substr($key,-2) == '>='        ||    substr($key,-2) == '<='        ||
            substr($key,-2) == '!='        ||
            substr($key,-2) == '!='    ||    substr($key,-4) == 'like'){
                $result .= $this->parseKey($key).' '.$this->parseValue($val);
        }elseif(substr($key,-2) == 'in' || substr($key,-6) == 'not in'){
            $result .= $this->parseKey($key)."(".$val.")";
        }else{    
            $result .= $this->parseKey($key)."='".$val."'";
        }    
        return $result;
    }
         
    /**
     |----------------------------------------------------------
     | 表名组合
     |----------------------------------------------------------
     | @param mixed $tableName 
     |----------------------------------------------------------
     | @return string
     |----------------------------------------------------------
     */    
    public function parseTable($tableName){
        return $this->parseKey(C("DB_PREFIX").$tableName);
    }
    /**
     |----------------------------------------------------------
     | 字段和表名处理添加`
     |----------------------------------------------------------
     | @return string
     +----------------------------------------------------------
     */
    public function parseKey($key) {
        $key   =  trim($key);
        if(!preg_match('/[,\'\"\*\(\)`.\s]/',$key)) {
           $key = '`'.$key.'` ';
        }elseif(substr($key,-6)  == 'not in'){
            $key = ' `'.trim(substr($key,0,strlen($key) - 6)).'` '.substr($key,-6).' '; 
        }elseif(substr($key,-1)  == '>' || substr($key,-1)  == '<' ){
            $key = ' `'.trim(substr($key,0,strlen($key) - 1)).'` '.substr($key,-1).' '; 
        }elseif(substr($key,-2)  == '>=' || substr($key,-2)  == '<=' || substr($key,-2) == '!=' || substr($key,-2) == 'in'){
            $key = ' `'.trim(substr($key,0,strlen($key) - 2)).'` '.substr($key,-2).' '; 
        }elseif(substr($key,-4)  == 'like'){
            $key = ' `'.trim(substr($key,0,strlen($key) - 4)).'` '.substr($key,-4).' '; 
        }
        return $key.' ';
    }    
    /**
     |----------------------------------------------------------
     | 设置锁机制
     |----------------------------------------------------------
     | @return string
     |----------------------------------------------------------
     */
    private function parseLock($lock=false) {
        if(!$lock) return '';
        if('ORACLE' == $this->dbType) {
            return ' FOR UPDATE NOWAIT ';
        }
        return ' FOR UPDATE ';
    }
    
    /**
     |----------------------------------------------------------
     | 解析JOIN
     |----------------------------------------------------------
     | @return string
     |----------------------------------------------------------
     */
     public function parseJoin($join){
        $joinStr = '';
        if($join){   
            foreach($join As $key => $val){
                if(is_array($val)){
                    $on = isset($val['ON']) ? $val['ON'] : $val['on'];
                    $joinStr .= ' LEFT JOIN '.$this->parseTable($val['table']).' As '.$key.' ON ('.$on.') ';
                }
            }       
        }
       
        return $joinStr;
     }
    /**
     |----------------------------------------------------------
     | 解析ORDER
     |----------------------------------------------------------
     | @return string
     |----------------------------------------------------------
     */
    public function parseOrder($order){
        $orderStr = '';
        if($order){
            $result = array();
            foreach($order As $key => $val){
                $result[] = ($this->parseKey($key)." ".$val);
            }
            $orderStr = 'ORDER BY '.implode(",",$result);
        }
        return $orderStr;
    }
    /**
     |----------------------------------------------------------
     | 解析GROUP
     |----------------------------------------------------------  
     | @return string
     |----------------------------------------------------------
     */    
    public function parseGroup($group){
        return !empty($group)? ' GROUP BY '.$group:''; 
    }
    
    /**
     |----------------------------------------------------------
     | 解析DISTIINCT
     |---------------------------------------------------------- 
     | @return string
     |----------------------------------------------------------
     */    
    public function parseDistinct($distinct) {
        return !empty($distinct)?   ' DISTINCT ' :'';
    }
    
    /**
     |----------------------------------------------------------
     | 解析HAVING
     |---------------------------------------------------------- 
     | @return string
     |----------------------------------------------------------
     */    
    public function parseHaving($having) {
        return  !empty($having)?   ' HAVING '.$having:'';
    }
    /**
     |----------------------------------------------------------
     | 解析LIMIT
     |---------------------------------------------------------- 
     | @return string
     |----------------------------------------------------------
     */    
    public function parseLimit($limit,$start = 0) {
        $start = $start ? ($start - 1) * $limit : 0;
        return !empty($limit)?   ' LIMIT '.$start.','.$limit.' ':'';
    }
    
   /**
     |----------------------------------------------------------
     | Value解析
     |---------------------------------------------------------- 
     | @return string
     |----------------------------------------------------------
     */     
    public function parseValue($value) {
        if(!$value) {
            $value   =  'null';    
        }else{
            $value = '\''.daddslashes($value).'\'';
        }
        return $value;
    }             	
}

?>