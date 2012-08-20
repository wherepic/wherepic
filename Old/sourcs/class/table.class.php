<?php

/*
|---------------------------------------------------------------
| 数据处理
|---------------------------------------------------------------
| Author CAO JIAYIN
| Mail caojiayin1984@gmail.com
| Last Date 2012-04-15
|---------------------------------------------------------------
*/

class table extends mysql{
	
	private $db = null;
	private $config = null;

	public function __construct($config = ''){
		parent::__construct();
		if(empty($config)){
			$config = main::getConfig('db');		
		}
		$this->config = $config;
		$this->db = parent::open($config);
	}
	function __destruct(){
		parent::close();    
	}
	
	/**
	*数据查询
	*@param $table  : 表名 
	*@param $params : 条件数组
	*$param $key : 主键
	*@return 返回数据/数组，如果是分页查询则返回（0：数据，1分页数据，2，统计）
	*/
	public function select($table,$params=null,$key=''){
		$table = $this->config['tablepre'] . $table;
		$paginate = isset($params['page']) ? true : false;
		$fields=isset($params['fields'])?implode(',',$params['fields']):'*';
		$params['page'] = max(1, intval($params['page']));
		$join=$this->format_left($params);
		$conditions=$this->format_condition($params);
		$orders=$this->format_order($params);
		$group = $this->format_group($params);
		$limit = $this->format_limit($params);
		$sql='select '.$fields.' from '.$table.' '.$join.' '.$conditions.' '.$group.' '.$orders.' '.$limit;
		//exit($sql);
		$res = parent::query($sql);
		$datalist = array();
		while($rs = parent::fetch_array($res)){
			if($key) {
				$datalist[$rs[$key]] = $rs;
			} else {
				$datalist[] = $rs;
			}  
		}
		parent::free_result();
		if($paginate){
			$paginate = $this->paginate($table,$params);
			return array($datalist,$paginate[1],$paginate[0]);
		}		
		return $datalist;		
	}
	/**
	*分页处理
	*/
	private function paginate($table,$params=null){	
		$params['fields'] = array('count(*) As nums');
		$datanum = $this->getCount($table,$params);	
		$pagesize = $params['limit'] ? $params['limit'] : 20;
		$url = $params['url'] ? $params['url'] : application::getCurrPageUrl();
		$pages = new pages($datanum,$params['page'],$pagesize,$url);
		$multipage = $pages->multipage;
		return array($datanums,$multipage);
	}
	
	/**
	*数据查询 返回一行
	*@return 返回数组
	*/
	public function getRows($table,$params){
		$len = strlen($this->config['tablepre']);
		if(substr($table,0,$len) != $this->config['tablepre']){
			$table = $this->config['tablepre'] . $table;
		}	
		
		$fields=isset($params['fields'])?implode(',',$params['fields']):'*';
		$join=$this->format_left($params);
		$conditions=$this->format_condition($params);
		$group = $this->format_group($params);
		$sql='select '.$fields.' from '.$table.' '.$join.' '.$conditions.' '.$group;
		return parent::fetch_first($sql);
	}	
	
	/**
	*数据查询 返回一个数据
	*@return 返回一个值
	*/
	public function getOne($table,$params){
		$len = strlen($this->config['tablepre']);
		if(substr($table,0,$len) != $this->config['tablepre']){
			$table = $this->config['tablepre'] . $table;
		}		
		if(!isset($params['field'])){return null;}
		$fields = $params['field'];
		$join=$this->format_left($params);
		$orders=$this->format_order($params);
		$conditions=$this->format_condition($params);
		$sql='select '.$fields.' from '.$table.' '.$join.' '.$conditions.' '.$orders.' limit 1';	
		//echo $sql;
		return parent::result_first($sql);	
	}
	/**
	*统计数据查询
	*@return 返回一个值
	*/	
	public function getCount($table,$params){
		$_params=array(
			'field' => isset($params['field']) ? $params['field'] : array('count(*) as nums'),
			'conditions'=>$params['conditions'],
		);	
		if(isset($params['group'])){
			$_params['group']=$params['group'];	
		}
		if(isset($params['LEFT'])){
			$_params['LEFT']=$params['LEFT'];
		}
		return $this->getOne($table,$_params);		
	}
	
	/**
	*向数据表内新增数据
	*@param $table : 表名
	*@param $params : 数据
	*@return 新增数据编号
	*/
	public function insert($table,$params,$replace = false){
		$table = $this->config['tablepre'] . $table;
		$fielddata = array_values($params['key']);
		$valuedata = array_values($params['value']);
		array_walk($fielddata, array($this, 'add_special_char'));
		array_walk($valuedata, array($this, 'escape_string'));	
		$field = implode (',', $fielddata);
		$value = implode (',', $valuedata);		
		$cmd = $this->replace ? 'REPLACE INTO ' : 'INSERT INTO ';
		$sql = $cmd.$table.'('.$field.') VALUES ('.$value.')';
		//echo $sql;
		parent::query($sql);
		return parent::insert_id();		
	}
	
	/*复制数据*/
	public function copyTotable($source,$target,$params){
		$source = $this->config['tablepre'] . $source;
		$target = $this->config['tablepre'] . $target;
		$skey = array_values($params['fields']['skey']);
		$tkey = array_values($params['fields']['tkey']);
		array_walk($skey, array($this, 'add_special_char'));		
		array_walk($tkey, array($this, 'add_special_char'));
		$conditions=$this->format_condition($params);
		$skey = implode (',', $skey);
		$tkey = implode (',', $tkey);
		$sql = 'insert '.$target.'('.$tkey.') select '.$skey.' from '.$source.' '.$conditions;
		parent::query($sql);
		return parent::insert_id();		
	}
	
	/**
	*更新数据
	*
	*/
	public function update($table,$params){
		$table = $this->config['tablepre'] . $table;
		$conditions=$this->format_condition($params);
		$fields = array();
		$value = $params['fields'];
		foreach($value as $k => $v) {
			switch (substr($k, -2,2)) {
				case '+=':
					$k = substr($k,0,-2);
					if (is_numeric($v)) {
						$fields[] = $this->add_special_char($k).'='.$this->add_special_char($k).'+'.$this->escape_string($v, '', false);
					}else{
						continue;
					}
					break;
				case '-=':
					$k= substr($k,0,-2);
					if (is_numeric($v)) {
						$fields[] = $this->add_special_char($k).'='.$this->add_special_char($k).'-'.$this->escape_string($v, '', false);
					} else {
						continue;
					}
					break;
				default:
					$fields[] = $this->add_special_char($k).'='.$this->escape_string($v);
			}
		}
		$field = implode(',', $fields);
		$sql = 'UPDATE '.$table.' SET '.$field.$conditions;
		parent::query($sql);	
	}
	/**
	*删除数据
	*/
	public function delete($table,$params){
		$table = $this->config['tablepre'] . $table;
		$conditions=$this->format_condition($params);	
		$sql='DELETE FROM '.$table.' '.$conditions;
		parent::query($sql);		
	}
	
	/**
	*直接运行mysql语句
	*@param $sql : 运行的mysql语句
	*@param $source : 是否返回数据源(resource(num) of type (mysql result) )
	*/
	public function RunSql($sql,$source = false){
		$res = parent::query($sql);
		if($source){return $res;}
		while($rs = parent::fetch_array($res)){
			$datalist[] = $rs;
		}		
		parent::free_result();	
		return $datalist;		
	}
	
	
	
	/**
	*格式化联合查询
	*@param $params
	*/
	public function format_left($param){
		$result = '';
		if(isset($param['LEFT'])){
			$left = $param['LEFT'];
			$result = '';
			foreach($left As $key => $val){
				if(is_array($val)){
					$result .= ' LEFT JOIN '.$this->config['tablepre'].$val['table'].' As '.$key.' ON ('.$val['ON'].') ';
				}
			}		
		}
		return $result;	
	}	
	
	/*格式化条件*/
	public function format_condition($params){
		$result = '';
		if(isset($params['conditions'])){	
			$result = ' WHERE ';
			$i = 0;
			foreach($params['conditions'] As $key => $val){				
				if($key != 'or'){
					$result .= $i ? " AND " : "";
					$result .= $this->format_sign($key,$val);
				}elseif($key == 'or'){
					$result .= $i ? " AND (" : "(";
					$n = 0;
					foreach($val As $k => $v){
						$result .= $n ? " OR " : "";
						if(is_array($v)){
							$temp = " ( ";
							$j = 0;
							foreach($v as $k1 => $v1){
								$temp .= $j ? " AND " : "";
								$temp .= $this->format_sign($k1,$v1);
								$j++;
							}
							unset($j);
							$temp .= " ) ";
							$result .= $temp;
						}else{						
							$result .= $this->format_sign($k,$v);
						}
						$n++;
					}
					$result .= ")";
				}
				$i++;
			}
		}
		return $result;
	}
	
	/**
	*格式化排序
	*/
	public function format_order($params){
		$result = '';
		if(isset($params['orders'])){
			$result = ' ORDER BY ';
			$i = 0;
			foreach($params['orders'] As $key => $val){
				$result .= $i ? ",".$key." ".$val : $key." ".$val;
				$i++;
			}
		}
		return $result;
	}
	/**
	*格式化分组
	*/	
	public function format_group($params){
		if(isset($params['group'])){
			return " GROUP BY ".$params['group'];
		}
		return '';
	}
	/**
	*格式化数据条数
	*/		
	public function format_limit($params){
		$result = '';
		$page = 0;
		if(isset($params['page'])){
			$page = $params['page'] - 1;
		}
		if(isset($params['limit'])){
			$start_limit = $page * $params['limit'];
			$result='LIMIT '.$start_limit.' , '.$params['limit'];
		}
		return $result;
	}
	/**
	 * 对字段两边加反引号，以保证数据库安全
	 * @param $value 数组值
	 */
	public function add_special_char(&$value) {
		if('*' == $value || false !== strpos($value, '(') || false !== strpos($value, '.') || false !== strpos ( $value, '`')) {
			//不处理包含* 或者 使用了sql方法。
		} else {
			$value = '`'.trim($value).'`';
		}
		return $value;
	}
	
	/**
	 * 对字段值两边加引号，以保证数据库安全
	 * @param $value 数组值
	 * @param $key 数组key
	 * @param $quotation 
	 */
	public function escape_string(&$value, $key='', $quotation = 1) {
		if ($quotation) {
			$q = '\'';
		} else {
			$q = '';
		}
		$value = $q.$value.$q;
		return $value;
	}	
	
	private function format_sign($key,$val){
		$result = '';
		if( substr($key,-2) == ' >'		||	substr($key,-2) == ' <'		||
			substr($key,-2) == '>='		||	substr($key,-2) == '<='		||
			substr($key,-2) == '!='		||
			substr($key,-3) == ' >='	||	substr($key,-3) == ' <='	||
			substr($key,-3) == ' !='	||	substr($key,-5) == ' like'){
				$result .= $key." '".$val."'";
		}elseif(substr($key,-3) == ' in' || substr($key,-7) == ' not in'){
			$result .= $key."(".$val.")";
		}else{	
			$result .= $key."='".$val."'";
		}	
		return $result;
	}
}

?>