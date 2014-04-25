<?php
class PostsTagFixture extends CakeTestFixture {
	var $name = 'PostsTag';
	var $import = array('table' => 'posts_tags');
	
	var $records = array (
			array (
					'id' => 1,
					'post_id' => 1,
					'tag_id' => 1,
					'created' => '2007-03-18 10:41:23'
			),
			array (
					'id' => 2,
					'post_id' => 1,
					'tag_id' => 2,
					'created' => '2007-03-18 10:41:23'
			),
			array (
					'id' => 3,
					'post_id' => 1,
					'tag_id' => 3,
					'created' => '2007-03-18 10:41:23'
			),
			array (
					'id' => 4,
					'post_id' => 2,
					'tag_id' => 4,
					'created' => '2007-03-18 10:41:23'
			),
			array (
					'id' => 5,
					'post_id' => 2,
					'tag_id' => 5,
					'created' => '2007-03-18 10:41:23'
			)
	);

}

?>
 