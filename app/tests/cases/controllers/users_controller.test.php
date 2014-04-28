<?php
App::import('Controller', 'Users');

class TestUsersController extends UsersController {
    var $name = 'Users';
 
    var $autoRender = false;
 
    function redirect($url, $status = null, $exit = true) {
        $this->redirectUrl = $url;
    }
 
    function render($action = null, $layout = null, $file = null) {
        $this->renderedAction = $action;
    }
 
    function _stop($status = 0) {
        $this->stopped = $status;
    }
}

class UsersControllerTest extends CakeTestCase {
	var $components = array('Session');
	var $fixtures = array('app.post', 'app.tag', 'app.posts_tag', 'app.user', 'app.admin');
	
	function startTest() {
		$this->Users = new TestUsersController();
		$this->Users->constructClasses();
		$this->Users->beforeFilter();
		$this->Users->Component->initialize($this->Users);
	}

	function endTest() {
		$this->Users->Session->destroy();
		unset($this->Users);
		ClassRegistry::flush();
	}
	
	function testRegister() {
		$this->Users->params = Router::parse('/users/register');
		// Assert successfully added
		$this->Users->data = array('User' => array (
				'email' => EMAIL_FOR_TEST_REGISTER_USER,
				'tmp_password' => '123456',
				'password_confirm' => '123456',
				'firstname' => 'nick',
				'lastname' => 'bamby'
			)
		);
		$this->Users->register();
		$user = $this->Users->User->findById(11);
		$this->assertEqual($user['User']['email'], EMAIL_FOR_TEST_REGISTER_USER);
		$this->assertEqual($user['User']['firstname'], 'nick');
		$this->assertEqual($user['User']['lastname'], 'bamby');
		$this->assertEqual($this->Users->Session->read('Message.flash.message'), 'Register successfully.');
		$this->assertEqual($this->Users->redirectUrl, array('action' => 'login'));

		// Assert validation: empty email
		$this->Users->data = array('User' => array (
				'email' => '',
				'tmp_password' => '123456',
				'password_confirm' => '123456',
				'firstname' => 'nick',
				'lastname' => 'bamby'
			)
		);
		$this->Users->register();
		$this->assertTrue(isset($this->Users->User->validationErrors['email']));
		
		// Assert validation: existed email
		$this->Users->data = array('User' => array (
				'email' => EMAIL_FOR_AUTH,
				'tmp_password' => '123456',
				'password_confirm' => '123456',
				'firstname' => 'nick',
				'lastname' => 'bamby'
			)
		);
		$this->Users->register();
		$this->assertTrue(isset($this->Users->User->validationErrors['email']));
		
		// Assert validation: invalid email
		$this->Users->data = array('User' => array (
				'email' => 'tran.thanh.son.gsc$$gmail.com',
				'tmp_password' => '123456',
				'password_confirm' => '123456',
				'firstname' => 'nick',
				'lastname' => 'bamby'
			)
		);
		$this->Users->register();
		$this->assertTrue(isset($this->Users->User->validationErrors['email']));
		
		// Assert validation: empty password
		$this->Users->data = array('User' => array (
				'email' => EMAIL_FOR_TEST_REGISTER_USER,
				'tmp_password' => '',
				'password_confirm' => '123456',
				'firstname' => 'nick',
				'lastname' => 'bamby'
			)
		);
		$this->Users->register();
		$this->assertTrue(isset($this->Users->User->validationErrors['tmp_password']));
		
		// Assert validation: matching password and password confirm
		$this->Users->data = array('User' => array (
				'email' => EMAIL_FOR_TEST_REGISTER_USER,
				'tmp_password' => '123456789',
				'password_confirm' => '123456',
				'firstname' => 'nick',
				'lastname' => 'bamby'
			)
		);
		$this->Users->register();
		$this->assertTrue(isset($this->Users->User->validationErrors['password_confirm']));
		
		// Assert validation: empty firstname
		$this->Users->data = array('User' => array (
				'email' => EMAIL_FOR_TEST_REGISTER_USER,
				'tmp_password' => '123456',
				'password_confirm' => '123456',
				'firstname' => '',
				'lastname' => 'bamby'
			)
		);
		$this->Users->register();
		$this->assertTrue(isset($this->Users->User->validationErrors['firstname']));
		
		// Assert validation: empty firstname
		$this->Users->data = array('User' => array (
				'email' => EMAIL_FOR_TEST_REGISTER_USER,
				'tmp_password' => '123456',
				'password_confirm' => '123456',
				'firstname' => 'nick',
				'lastname' => ''
			)
		);
		$this->Users->register();
		$this->assertTrue(isset($this->Users->User->validationErrors['lastname']));
	}

	function testActive() {		
		$this->Users->params = Router::parse('/users/active');
		// Assert active successfully
		$this->Users->active('d98214490ec4d06b9ac52444ea2574db82f7e49a', 2);
		$user = $this->Users->User->findById(2);
		$this->assertEqual($user['User']['active'], 1);
		$this->assertEqual($this->Users->redirectUrl, array('action' => 'login'));

		// Assert active fail: wrong token_hash)
		$this->Users->active('d98214490ec4d06b9ac52444ea2574db82f7e49a123456789', 3);
		$user = $this->Users->User->findById(3);
		$this->assertEqual($user['User']['active'], 0);
		$this->assertEqual($this->Users->Session->read('Message.flash.message'), 'Your registration failed please try again');
		
		// Assert active fail: user is activated
		$this->Users->active('d98214490ec4d06b9ac52444ea2574db82f7e49a', 1);
		$this->assertEqual($this->Users->Session->read('Message.flash.message'), 'Token has alredy been used');
	}
	
	function testLogin() {	
		$this->Users->params = Router::parse('/users/login');
		// Assert login successfully
		$this->Users->data = array('User' => array (
				'email' => EMAIL_FOR_AUTH,
				'password' => '123456'
			)
		);
		$this->Users->login();
		//$this->assertEqual($this->Users->Session->read('Auth.User.id'), 1);
		//$this->assertEqual($this->Users->Session->read('Auth.User.email'), EMAIL_FOR_AUTH);
		//$this->assertEqual($this->Users->redirectUrl, '../users');

		// Assert login fail (usernme or password is not true)
		$data = array('User' => array (
				'email' => 'thanhson@gmail.com',
				'password' => '123456'
			)
		);
		$result = $this->testAction ('/users/login', array (
				'return' => 'vars',
				'data' => $data,
				'method' => 'post'
		));
		$this->assertEqual($this->Users->Session->read('Message.auth.message'), 'Failed to login');
	
		// Assert login fail (active = 0)
		$data = array('User' => array (
				'username' => 'tran.thanh.son@gmail.com',
				'password' => '123456'
			)
		);
		$result = $this->testAction ('/users/login', array (
				'return' => 'vars',
				'data' => $data,
				'method' => 'post'
		));
		$this->assertEqual($this->Users->Session->read('Message.auth.message'), 'Failed to login');
	}

	function testLogout() {
		// write session for authentication
		$this->Users->Session->write('Auth.User', array(
				'id' => 1,
				'username' => EMAIL_FOR_AUTH,
		));
		$this->assertEqual($this->Users->Session->read('Auth.User.id'), 1);
		$this->assertEqual($this->Users->Session->read('Auth.User.username'), EMAIL_FOR_AUTH);
		
		$this->Users->params = Router::parse('/users/logout');
		$this->Users->logout();
		$this->assertEqual($this->Users->Session->read('Auth.User.id'), null);
		$this->assertEqual($this->Users->Session->read('Auth.User.username'), null);
		$this->assertEqual($this->Users->redirectUrl, '/users/login');
	}
	
	function testAdmin_Login() {
		$this->Users->params = Router::parse('/admin/users/login');
		// Assert login successfully
		$this->Users->data = array('Admin' => array (
				'username' => ADMIN_USERNAME,
				'password' => ADMIN_PASSWORD
			)
		);
		$this->Users->admin_login();
		$this->assertEqual($this->Users->Session->read('admin.username'), ADMIN_USERNAME);
		$this->assertEqual($this->Users->redirectUrl, array('action' => 'index'));
		
		// Assert login fail
		$this->Users->data = array('Admin' => array (
				'username' => '',
				'password' => ADMIN_PASSWORD
		)
		);
		$this->Users->admin_login();
		$this->assertTrue(isset($this->Users->Admin->validationErrors['username']));
		
		// Assert login fail
		$this->Users->data = array('Admin' => array (
				'username' => ADMIN_USERNAME,
				'password' => ''
		)
		);
		$this->Users->admin_login();
		$this->assertTrue(isset($this->Users->Admin->validationErrors['password']));		
	}
	
	function testAdmin_manageUsers() {
		// auth admin
		$this->Users->Session->write('admin.username', ADMIN_USERNAME);
	
		$result = $this->testAction ('/admin/Users/manageUsers', array(
				'return' => 'vars'
		));
		// Assert post count
		$this->assertEqual(count($result['users']), 4);
		$userIds = Set::extract($result['users'], '{n}.User.id');
		// Assert post ids
		$this->assertEqual($userIds, array(1, 2, 3, 4));
		// Check data in page 2
		$result = $this->testAction ('/admin/Users/manageUsers/page:2', array(
				'return' => 'vars'
		));
		// Assert post count
		$this->assertEqual(count($result['users']), 4);
		$userIds = Set::extract($result['users'], '{n}.User.id');
		// Assert post ids
		$this->assertEqual($userIds, array(5, 6, 7, 8));
		// Check data in page 3
		$result = $this->testAction ('/admin/Users/manageUsers/page:3', array(
				'return' => 'vars'
		));
		// Assert post count
		$this->assertEqual(count($result['users']), 2);
		$userIds = Set::extract($result['users'], '{n}.User.id');
		// Assert post ids
		$this->assertEqual($userIds, array(9, 10));
		
		// check search data with keyword = firstname
		$result = $this->testAction ('/admin/Users/manageUsers/keyword:firstname', array (
				'return' => 'vars'
		));
		$this->assertEqual(count($result['users']), 1);
		$this->assertEqual($result['users'][0]['User']['id'], 4);
		$this->assertEqual($result['users'][0]['User']['email'], 'tran.thanh.son4444@gmail.com');
		$this->assertEqual($result['users'][0]['User']['lastname'], 'lastname');
	}
	
	function admin_delete($id) {
		$this->Users->Session->write('admin.username', ADMIN_USERNAME);
		
		// check info of post which have id equal 1 before delete
		$this->assertEqual($this->Users->User->find('count'), 10);
		$this->assertEqual($this->Users->Post->find('count'), 10);
		$this->assertEqual($this->Users->PostsTag->find('count'), 5);
		
		$this->Users->params = Router::parse('/admin/users/delete');
		// Assert failed case: id doesn't exist
		$this->Users->admin_delete(11);
		$this->assertEqual($this->Users->User->find('count'), 10);
		$this->assertEqual($this->Users->Post->find('count'), 10);
		$this->assertEqual($this->Users->PostsTag->find('count'), 5);
		// Assert successful case
		$this->Users->admin_delete(1);
		$this->assertEqual($this->Users->User->find('count'), 9);
		$this->assertEqual($this->Users->Post->find('count'), 0);
		$this->assertEqual($this->Users->PostsTag->find('count'), 0);
		$this->assertEqual($this->Users->Session->read('Message.flash.message'), 'The user with id: 1 has been deleted.');
		$this->assertEqual($this->Users->redirectUrl, array('action' => 'admin_manageUsers'));
	}
	
	function testAdmin_edit() {
		$this->Users->Session->write('admin.username', ADMIN_USERNAME);
	
		// check info of post which have id equal 1 before edit
		$post = $this->Users->User->read(null, 2);
		$this->assertEqual($post['User']['email'], 'tran.thanh.son@gmail.com');
		$this->assertEqual($post['User']['firstname'], 'nick');
		$this->assertEqual($post['User']['lastname'], 'bamby');
		
		$this->Users->data = array(
				'User' => array(
						'id' => 2,
						'email' => 'thanh.son@gmail.com',
						'firstname' => 'tom',
						'lastname' => 'hank'
				)
		);
		$this->Users->params = Router::parse('/admin/users/edit');
		$this->Users->admin_edit(2);
		// Assert successfully updated
		$post = $this->Users->User->read(null, 2);
		$this->assertEqual($post['User']['email'], 'thanh.son@gmail.com');
		$this->assertEqual($post['User']['firstname'], 'tom');
		$this->assertEqual($post['User']['lastname'], 'hank');
		$this->assertEqual($this->Users->Session->read('Message.flash.message'), 'User has been updated.');
		$this->assertEqual($this->Users->redirectUrl, array('action' => 'admin_manageUsers'));
		// Assert validation with email required
		$this->Users->data = array('User' => array (
				'id' => 5,
				'email' => '',
				'firstname' => 'tom',
				'lastname' => 'hank'
			)
		);
		$this->Users->admin_edit(5);
		$this->assertTrue(isset($this->Users->User->validationErrors['email']));
		// Assert validation with firstname required
		$this->Users->data = array('User' => array (
				'id' => 5,
				'email' => 'thanh.son@gmail.com',
				'firstname' => '',
				'lastname' => 'hank'
			)
		);
		$this->Users->admin_edit(5);
		$this->assertTrue(isset($this->Users->User->validationErrors['firstname']));
		// Assert validation with firstname required
		$this->Users->data = array('User' => array (
				'id' => 5,
				'email' => 'thanh.son@gmail.com',
				'firstname' => 'Tom',
				'lastname' => ''
			)
		);
		$this->Users->admin_edit(5);
		$this->assertTrue(isset($this->Users->User->validationErrors['lastname']));
	}
	
	function admin_logout() {
		$this->Users->Session->write('admin.username', ADMIN_USERNAME);
		
		$this->Users->params = Router::parse('/admin/users/logout');
		$this->Users->admin_logout();
		$this->assertEqual($this->Users->Session->read('admin.username'), null);
	}
}
?>