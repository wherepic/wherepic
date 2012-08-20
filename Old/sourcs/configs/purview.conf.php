<?php

/*
模块,及模块的方法，列出来的方式将会进行权限控制，如果不需要进行权限控制则可以去掉
$purview['模块标识'] = array(
	'name' => '模块名名称',
	'func' => array(
		array('方法标识' => '方法名称'),
		array('方法标识' => '方法名称'), //如果此方法不需要权限控制则可以直接去掉
		array('方法标识' => '方法名称'),
		array('方法标识' => '方法名称'),
		array('方法标识' => '方法名称'),
	),
);
*/
$purview = array(); 
$purview['system'] = array(
	'name' => '系统管理',
	'func' => array(
		array('basic' => '基本信息'),
	),
);

$purview['group'] = array(
	'name' => '管理权限',
	'func' => array(
		array('list' => '权限列表'),
		array('add' => '添加'),
	),
);
 
/*
菜单,对应模块
$nav['菜单标识'] = array(
	'name' => '一级菜单名称',
	'list' => array(
		'模块标识' => array(
			array('方法标识' => '方法名称'),
			array('方法标识' => '方法名称'),
		),
		'模块标识' => array(
			array('方法标识' => '方法名称'),
			array('方法标识' => '方法名称'),
		),		
	),
);
	
*/
$nav = array();
$nav['index'] = array(
	'name' => '控制面板',
	'list' => array(
		'index' => array(
			'init' => '首页',
			'comment' => '评论',
			'mail' => '邮件',
		),
		'group' => array(
			'list' => '管理权限',
		),		
	),
);
$nav['system'] = array(
	'name' => '系统管理',
	'list' => array(
		'system' => array(
			'basic' => '基本信息',
		),
		'group' => array(
			'list' => '管理权限',
		),
	),
);


return array('purview' => $purview,'nav' => $nav);
?>