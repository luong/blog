<?php

class PostsTag extends AppModel {
    var $name = 'PostsTag';
    var $actsAs = array('Containable');
    
    var $belongsTo = array(
    		'Post' => array(
    				'className' => 'Post',
    				'foreignKey' => 'post_id'
    		),
    		'Tag' => array(
    				'className' => 'Tag',
    				'foreignKey' => 'tag_id'
    		),
    );

}

?>