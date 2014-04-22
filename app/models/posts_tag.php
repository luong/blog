<?php

class PostsTag extends AppModel {
    var $name = 'PostsTag';
    
    var $belongsTo = array(
    		'Post',
    		'Tag'
    );

}

?>