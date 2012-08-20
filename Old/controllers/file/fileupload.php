<?php
 class fileupload extends core{
 	private $upload;
  public function __construct(){
 		parent::__construct();
 		$this->upload = new upload();
 	}	
 	public function aaa(){
 		include $this->template('index');
 	}
 	public function admin(){
 		$file = $_FILES['Filedata'];
 		$this->upload->start($file);
 	}
 }
 ?>