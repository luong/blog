<?php
class AdminFixture extends CakeTestFixture {
	var $name = 'Admin';
	var $import = array('table' => 'admins');

	var $records = array (
			array (
					'username' => ADMIN_USERNAME,
					'password' => ADMIN_PASSWORD
			)
	);
}

?>
 