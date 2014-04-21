<?php
class TagFixture extends CakeTestFixture {
	var $name = 'Tag';
	var $import = array('table' => 'tags');

	var $records = array (
			array (
					'id' => 1,
					'name' => 'tag 1'
			),
			array (
					'id' => 2,
					'name' => 'tag 2'
			),
			array (
					'id' => 3,
					'name' => 'tag 3'					
			),
			array (
					'id' => 4,
					'name' => 'tag 4'
			),
			array (
					'id' => 5,
					'name' => 'tag 5',
			)
	);
}

?>
 