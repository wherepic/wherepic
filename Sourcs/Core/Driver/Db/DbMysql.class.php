<?php

/**
|---------------------------------------------------------------
| Mysql 处理类
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------
*/
Class DbMysql{
	private $Config = array();
    private $linkID = null;
    private $queryID = null;
    
	public function __construct($config) {
        if (!extension_loaded('mysql')){
           CLog::write(str_replace('%s%',__CLASS__,L('ERR_DB_ON_FOUND_DATABASE')));
        }
		$this->Config = $config;
		return $this->Connent();
	}	

	/**
	|---------------------------------------------------------------
	| Mysql链接
	|---------------------------------------------------------------
	*/	
	public function Connent(){
	    if($this->linkID){
            return $this->linkID;
        }
        $Host = $this->Config['HOST'].($this->Config['PORT']?":{$this->Config['PORT']}":'');
        if($this->Config['CONNENT']) {
            $this->linkID = mysql_pconnect($Host, $this->Config['USERNAME'], $this->Config['PASSWORD']);
        }else{
            $this->linkID = mysql_connect($Host, $this->Config['USERNAME'], $this->Config['PASSWORD'],1);
        }
        
        if (!$this->linkID || (!empty($this->Config['DATABASE']) && !mysql_select_db($this->Config['DATABASE'], $this->linkID)) ) {
            CLog::write(mysql_error());
        }
        $Version = $this->getVersion();
        if ($Version >= '4.1') {
            //使用UTF8存取数据库 需要mysql 4.1.0以上支持
            $this->Query("SET NAMES '".$this->Config['CHARSET']."'");
        }
        //设置 sql_model
        if($Version >'5.0.1'){
            $this->Query("SET sql_mode=''");
        }
        return $this->linkID;              
	}
    
    /**
    |---------------------------------------------------------------
    | 数据库查询执行方法
    |---------------------------------------------------------------
    */        
    public function Query($sql){
        if(!is_resource($this->linkID)) {
            $this->Connent();
        }
        if($this->queryID){$this->Free();}
        $this->queryID = mysql_query($sql, $this->linkID) or CLog::write(mysql_error());
        return $this->queryID;       
    }
    /**
     |----------------------------------------------------------
     | 遍历查询结果集
     |----------------------------------------------------------
     */
    function FetchArray($sql, $key='', $result_type = MYSQL_ASSOC) {
        $this->Query($sql);
        $result = array();
        if(mysql_affected_rows($this->linkID) > 0){
            while($row = mysql_fetch_array($this->queryID,$result_type)){
                if($key){
                    $result[$row[$key]]   =   $row;
                }else{
                    $result[]   =   $row;
                }              
            }
            mysql_data_seek($this->queryID,0);        
        }
        return $result;
    }
    
    /**
     |----------------------------------------------------------
     | 获取最后一次添加记录的主键号
     |----------------------------------------------------------
     */
    public function InsertID() {
        return mysql_insert_id($this->linkID);
    }
     
    /**
     |----------------------------------------------------------
     | 获取最后数据库操作影响到的条数
     |----------------------------------------------------------
     */
    public function AffectedRows() {
        return mysql_affected_rows($this->linkID);
    }
    
    /**
     |----------------------------------------------------------
     | 从结果数据中取第一行数据
     |----------------------------------------------------------
     */     
    function ResultRow($sql, $result_type = MYSQL_ASSOC) {
        $this->Query($sql);
        return mysql_fetch_array($this->queryID,$result_type);
    }
    
    /**
     |----------------------------------------------------------
     | 从结果数据中取第一行一列数据
     |----------------------------------------------------------
     */    
    function ResultColumn($sql) {
        return @mysql_result($this->Query($sql), 0);
    }
           
    /**
     |----------------------------------------------------------
     | 释放查询结果
     |----------------------------------------------------------
     | @access public
     |----------------------------------------------------------
     */
    public function Free() {
        @mysql_free_result($this->queryID);
        $this->queryID = null;
    } 
    
    /**
     |----------------------------------------------------------
     | 取得数据表的字段信息
     |----------------------------------------------------------
     | @access public
     |----------------------------------------------------------
     */
    public function getFields($tableName) {
        $result =   $this->FetchArray('SHOW COLUMNS FROM '.$tableName);
        $info   =   array();
        if($result) {
            foreach ($result as $key => $val) {
                $info[$val['Field']] = array(
                    'name'    => $val['Field'],
                    'type'    => $val['Type'],
                    'notnull' => (bool) ($val['Null'] === ''), // not null is empty, null is yes
                    'default' => $val['Default'],
                    'primary' => (strtolower($val['Key']) == 'pri'),
                    'autoinc' => (strtolower($val['Extra']) == 'auto_increment'),
                );
            }
        }
        return $info;
    }    

    /**
     |----------------------------------------------------------
     | 取得数据库的表信息
     |----------------------------------------------------------
     */
    public function getTables($dbName='') {
        if(!empty($dbName)) {
           $sql    = 'SHOW TABLES FROM '.$dbName;
        }else{
           $sql    = 'SHOW TABLES ';
        }
        $result =   $this->FetchArray($sql);
        $info   =   array();
        foreach ($result as $key => $val) {
            $info[$key] = current($val);
        }
        return $info;
    }    
    
    /**
     |----------------------------------------------------------
     | 关闭数据库链接
     |----------------------------------------------------------
     | @access public
     |----------------------------------------------------------
     */
    public function Close() {
        if ($this->linkID){
            mysql_close($this->linkID);
        }
        $this->linkID = null;
    }    
    /**
    |---------------------------------------------------------------
    | 获取数据库版本
    |---------------------------------------------------------------
    */        
    public function getVersion() {
        if(!is_resource($this->linkID)) {
            $this->Connent();
        }
        return mysql_get_server_info($this->linkID);
    }    
    	
}

?>