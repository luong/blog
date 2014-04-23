<?php
class UserFixture extends CakeTestFixture {
	var $name = 'User';
	var $import = array('table' => 'users');

	var $records = array (
			array (
					'id' => 1,
					'username' => 'user1111',
					'email' => 'tran.thanh.son.gsc@gmail.com',
					'password' => '123456',
					'firstname' => 'nick',
					'lastname' => 'bamby',
					'active' => 1,
					'created' => '2007-03-18 10:41:23',
					'modified' => '2007-03-18 10:43:31'
			),
			array (
					'id' => 2,
					'username' => 'user2222',
					'email' => 'tran.thanh.son.gsc@gmail.com',
					'password' => '123456',
					'firstname' => 'nick',
					'lastname' => 'bamby',
					'active' => 0,
					'created' => '2007-03-18 10:41:23',
					'modified' => '2007-03-18 10:43:31'
			)
	);
}

?>
 