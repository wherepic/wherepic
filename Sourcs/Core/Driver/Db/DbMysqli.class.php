<?php

/**
|---------------------------------------------------------------
| Mysqli 处理类
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------
*/
Class DbMysqli{
	private $Config = array();
    private $linkID = null;
    private $queryID = null;
    
	public function __construct($config) {
        if (!extension_loaded('mysqli')){
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
        $this->linkID = new mysqli($this->Config['HOST'], $this->Config['USERNAME'], $this->Config['PASSWORD'], $this->Config['DATABASE']);
        if(mysqli_connect_errno()) CLog::write(mysqli_connect_error());
        $Version = $this->linkID->server_version;
        if ($dbVersion >= '4.1') {
            // 设置数据库编码 需要mysql 4.1.0以上支持
            $this->Query("SET NAMES '".$this->Config['CHARSET']."'");
        }
        //设置 sql_model
        if($dbVersion >'5.0.1'){
            $this->Query("SET sql_mode=''");
        } 
        $this->linkID->set_charset($this->Config['CHARSET']);       
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
        $this->queryID = $this->linkID->query($sql) or CLog::write($this->linkID->error.' ['.$this->linkID->errno.']');
        return $this->queryID;       
    }
    /**
     |----------------------------------------------------------
     | 遍历查询结果集
     |MYSQLI_ASSOC,MYSQLI_NUM,MYSQLI_BOTH
     |----------------------------------------------------------
     */
    function FetchArray($sql, $key = '', $result_type = MYSQLI_ASSOC) {
        $this->Query($sql);
        $result = array();
        $numRows = $this->linkID->affected_rows;      
        if($numRows > 0){
            for($i=0;$i<$numRows ;$i++ ){
                $res = $this->queryID->fetch_assoc();
                if($key){
                    $result[$res[$key]] = $res;
                }else{
                    $result[$i] = $res;
                }
            }
            $this->queryID->data_seek(0);        
        }
        return $result;
    }
    
    /**
     |----------------------------------------------------------
     | 获取最后一次添加记录的主键号
     |----------------------------------------------------------
     */
    public function InsertID() {
        return $this->linkID->insert_id;
    } 
    /**
     |----------------------------------------------------------
     | 获取最后数据库操作影响到的条数
     |----------------------------------------------------------
     */
    public function AffectedRows() {
        return $this->linkID->affected_rows;
    }
    
    function ResultRow($sql) {
         $this->Query($sql);
         if($this->linkID->affected_rows){
             return $this->queryID->fetch_assoc();
         }         
        //return $this->FetchArray($sql);
    }
    /**
     |----------------------------------------------------------
     | 从结果数据中取第一行数据
     |----------------------------------------------------------
     */    
    function ResultColumn($sql) {
         $this->Query($sql);
         if($this->linkID->affected_rows){             
             $this->queryID->data_seek(0);
             $res = $this->queryID->fetch_row();
             return $res[0];
         }
    }        
    /**
     |----------------------------------------------------------
     | 释放查询结果
     |----------------------------------------------------------
     | @access public
     |----------------------------------------------------------
     */
    public function Free() {
        $this->queryID->free_result();
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