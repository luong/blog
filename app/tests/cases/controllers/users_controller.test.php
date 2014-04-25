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
	var $fixtures = array('app.post', 'app.tag', 'app.posts_tag', 'app.user');
	
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
		$user = $this->Users->User->findById(4);
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
		$this->assertEqual($this->Users->Session->read('Auth.User.id'), 1);
		$this->assertEqual($this->Users->Session->read('Auth.User.email'), EMAIL_FOR_AUTH);
		$this->assertEqual($this->Users->redirectUrl, '../posts');

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
}
?>