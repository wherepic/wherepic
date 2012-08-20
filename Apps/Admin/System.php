<?php
 class System extends Admin{
 	public function __construct(){
 		parent::__construct();
 	}
 	public function basic(){
        $model = D();
 		$setting = $model->getRow();		
 		include T('system_basic');
 	}
 	public function update(){
 		$d = $_POST;
 		$comment = serialize($d['comment']);
 		$mail = serialize($d['mail']);
 		$this->db->update("settings",array(
 			'fields' => array(
 				'sitename' => $d['sitename'],
 				'keyword' => $d['keyword'],
 				'sitedesc' => $d['sitedesc'],
 				'userregedit' => $d['userregedit'],
 				'verifcode' => $d['verifcode'],
 				'veriftype' => $d['veriftype'],
 				'veriflen' => $d['veriflen'],
 				'comment' => $comment,
 				'comment_keyword' => $d['comment_keyword'],
 				'comment_blacklist' => $d['comment_blacklist'],
 				'mail' => $mail
 			),
 		));
  		$data = $this->db->getRows("settings",array(
 			'orders' => array('id' => 'DESC'),
 		));
 		$data['mail'] = unserialize($data['mail']);
 		$data['comment'] = unserialize($data['comment']);
 		$data['mail']['password'] = $this->crypt($data['mail']['password'],'code'); 
 		$this->cache->set("settings",$data);	
 		$this->showmessage($this->lang->get('pop_succ'),'page');
 	}
 }
?>