<?php

class Post extends AppModel {
    var $name = 'Post';
    var $useTable = "posts";
    
    var $validate = array(
    		'title' => array(
    				'rule' => 'notEmpty'
    		),
    		'body' => array(
    				'rule' => 'notEmpty'
    		)
    );
    
}

?>