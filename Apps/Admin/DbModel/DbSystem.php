<?php

Class DbSystem extends CDb{
    private $TableName = 'settings';
    public function __construct(){
       parent::__construct();
    }
    public function getRow(){
        return parent::DbgetRow($this->TableName,array(
            'orders' => array('id' => 'DESC'),
        ));
    }   
}

?>