<?php

class Post extends AppModel {
    var $name = 'Post';
    
    var $hasAndBelongsToMany = array(
	    	'Tag' => array (
					'className' => 'Tag',
					'joinTable' => 'posts_tags',
					'foreignKey' => 'post_id',
					'associationForeignKey' => 'tag_id'
		));

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