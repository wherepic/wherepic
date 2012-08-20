<?php

/*
|---------------------------------------------------------------
| mysql数据库实现类
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------
*/

class mysql{
	
	/**
	 * 数据库配置信息
	 */
	private $config = null;
	
	/**
	 * 数据库连接资源句柄
	 */
	public $link = null;

	/**
	 * 最近一次查询资源句柄
	 */
	public $lastqueryid = null;
			
	/**
	 *  统计数据库查询次数
	 */
	public $querycount = 0;
	
	public function __construct() {

	}
	/**
	 * 打开数据库连接,有可能不真实连接数据库
	 * @param $config    数据库连接参数
	 *             
	 * @return void
	 */
	public function open($config) {
		$this->config = $config;
		return $this->connect();
	}
	
	/**
	 * 开启数据库连接
	 *             
	 * @return void
	 */
	public function connect() {
		$func = $this->config['pconnect'] == 1 ? 'mysql_pconnect' : 'mysql_connect';
		if(!$this->link = @$func($this->config['dbhost'], $this->config['dbuser'], $this->config['dbpw'], 1)) {
			$this->halt('Can not connect to MySQL server');
			return false;
		}

		if($this->version() > '4.1') {
			$charset = isset($this->config['dbcharset']) ? $this->config['dbcharset'] : '';
			$serverset = $charset ? "character_set_connection='$charset',character_set_results='$charset',character_set_client=binary" : '';
			$serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',')." sql_mode='' ") : '';
			$serverset && mysql_query("SET $serverset", $this->link);        
		}

		if($this->config['database'] && !@mysql_select_db($this->config['database'], $this->link)) {
			$this->halt('Cannot use database '.$this->config['database']);
			return false;
		}
		$this->database = $this->config['database'];
		return $this->link;
	}
	
	/**
	 * 数据库查询执行方法
	 * @param $sql 要执行的sql语句
	 * @return 查询资源句柄
	 */
	public function query($sql) {
		if(!is_resource($this->link)) {
			$this->connect();
		}

		$this->lastqueryid = mysql_query($sql, $this->link) or $this->halt(mysql_error(), $sql);

		$this->querycount++;
		return $this->lastqueryid;
	}
	
	/**
	 * 遍历查询结果集
	 * @param $type        返回结果集类型    
	 *                     MYSQL_ASSOC，MYSQL_NUM 和 MYSQL_BOTH
	 * @return array
	 */
	function fetch_array($query, $result_type = MYSQL_ASSOC) {
		return mysql_fetch_array($query, $result_type);
	} 
			   
	/**
	 * 释放查询资源
	 * @return void
	 */
	public function free_result() {
		if(is_resource($this->lastqueryid)) {
			mysql_free_result($this->lastqueryid);
			$this->lastqueryid = null;
		}
	}

	/**
	 * 获取最后一次添加记录的主键号
	 * @return int 
	 */
	public function insert_id() {
		return mysql_insert_id($this->link);
	}
	
	/**
	 * 获取最后数据库操作影响到的条数
	 * @return int
	 */
	public function affected_rows() {
		return mysql_affected_rows($this->link);
	}
	
	/**
	* 获取行数
	* 
	* @param mixed $sql
	* @return int
	*/
	public function num_rows($sql) {
		$this->lastqueryid = $this->query($sql);
		return mysql_num_rows($this->lastqueryid);
	}

	/**
	* 获取列数
	* 
	* @param mixed $sql
	* @return int
	*/
	public function num_fields($sql) {
		$this->lastqueryid = $this->query($sql);
		return mysql_num_fields($this->lastqueryid);
	}
	
	function fetch_first($sql) {
		return $this->fetch_array($this->query($sql));
	}
	function result_first($sql) {
		return $this->result($this->query($sql), 0);
	}
	function result($query, $row = 0) {
		$query = @mysql_result($query, $row);
		return $query;
	}	
	public function error() {
		return @mysql_error($this->link);
	}

	public function errno() {
		return intval(@mysql_errno($this->link)) ;
	}
	
	public function version() {
		if(!is_resource($this->link)) {
			$this->connect();
		}
		return mysql_get_server_info($this->link);
	}
	
	function close() {
		return mysql_close($this->link);
	}       
	public function halt($message = '', $sql = '') {
		$this->errormsg = "<b>MySQL Query : </b> $sql <br /><b> MySQL Error : </b>".$this->error()." <br /> <b>MySQL Errno : </b>".$this->errno()." <br /><b> Message : </b> $message";
		$msg = $this->errormsg;
		echo '<div style="font-size:12px;text-align:left; border:1px solid #9cc9e0; padding:1px 4px;color:#000000;font-family:Arial, Helvetica,sans-serif;"><span>'.$msg.'</span></div>';
		exit;
	}                             
}
?>