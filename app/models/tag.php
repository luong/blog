<?php

class Tag extends AppModel {
    var $name = 'Tag';
    var $actsAs = array('Containable');
    
    var $hasMany = array(
    		'PostsTag' => array(
    				'className' => 'PostsTag',
    				'foreignKey' => 'tag_id',
    				'dependent' => true
    		)
    );

}

?>