<?php

class Post extends AppModel {
    var $name = 'Post';
    
    /*var $hasAndBelongsToMany = array(
	    	'Tag' => array (
					'className' => 'Tag',
					'joinTable' => 'posts_tags',
					'foreignKey' => 'post_id',
					'associationForeignKey' => 'tag_id'
		));*/
    
    var $hasMany = array(
    		'PostsTag' => array(
    				'className' => 'PostsTag',
    				'foreignKey' => 'post_id',
    				'dependent' => true
    		)
    	);
    
    var $belongsTo = array(
    		'User' => array(
    				'className' => 'User',
    				'foreignKey' => 'user_id'
    		)
    	);
    
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