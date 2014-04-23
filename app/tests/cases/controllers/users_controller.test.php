<?php
App::import('Controller', 'Users');
App::import('Component', 'Session');
Mock::generate('SessionComponent');

class TestUsersController extends UsersController {
    var $name = 'USers';
 
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
	var $fixtures = array('app.user', 'app.post');
	
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
				'username' => 'user3333',
				'email' => 'tran.thanh.son.gsc@gmail.com',
				'password' => 'e20942c3cff85f77346d865785b5763363292794',
				'password_confirm' => '123456',
				'firstname' => 'nick',
				'lastname' => 'bamby'
			)
		);
		$this->Users->register();
		$user = $this->Users->User->findById(3);
		$this->assertEqual($user['User']['username'], 'user3333');
		$this->assertEqual($user['User']['email'], 'tran.thanh.son.gsc@gmail.com');
		$this->assertEqual($this->Users->Session->read('Message.flash.message'), 'Register successfully.');
		$this->assertEqual($this->Users->redirectUrl, array('action' => 'login'));
				
		// Assert validation with empty username
		$this->Users->data = array('User' => array (
				'username' => '',
				'email' => 'tran.thanh.son.gsc@gmail.com',
				'password' => 'e20942c3cff85f77346d865785b5763363292794',
				'password_confirm' => '123456',
				'firstname' => 'nick',
				'lastname' => 'bamby'
			)
		);
		$this->Users->register();
		$this->assertTrue(isset($this->Users->User->validationErrors['username']));
		
		// Assert validation with exists username
		$this->Users->data = array('User' => array (
				'username' => 'user2222',
				'email' => 'tran.thanh.son.gsc@gmail.com',
				'password' => 'e20942c3cff85f77346d865785b5763363292794',
				'password_confirm' => '123456',
				'firstname' => 'nick',
				'lastname' => 'bamby'
			)
		);
		$this->Users->register();
		$this->assertEqual($this->Users->Session->read('Message.flash.message'), 'This username is exists.');
		
		// Assert validation with username which contain special character
		$this->Users->data = array('User' => array (
				'username' => 'user@4444',
				'email' => 'tran.thanh.son.gsc@gmail.com',
				'password' => 'e20942c3cff85f77346d865785b5763363292794',
				'password_confirm' => '123456',
				'firstname' => 'nick',
				'lastname' => 'bamby'
			)
		);
		$this->Users->register();
		$this->assertTrue(isset($this->Users->User->validationErrors['username']));
		
		// Assert validation with username which has over 20 charaxter
		$this->Users->data = array('User' => array (
				'username' => 'user44444444444444444444',
				'email' => 'tran.thanh.son.gsc@gmail.com',
				'password' => 'e20942c3cff85f77346d865785b5763363292794',
				'password_confirm' => '123456',
				'firstname' => 'nick',
				'lastname' => 'bamby'
			)
		);
		$this->Users->register();
		$this->assertTrue(isset($this->Users->User->validationErrors['username']));
		
		// Assert validation with empty email
		$this->Users->data = array('User' => array (
				'username' => 'user4444',
				'email' => '',
				'password' => 'e20942c3cff85f77346d865785b5763363292794',
				'password_confirm' => '123456',
				'firstname' => 'nick',
				'lastname' => 'bamby'
			)
		);
		$this->Users->register();
		$this->assertTrue(isset($this->Users->User->validationErrors['email']));
		
		// Assert validation with invalid email
		$this->Users->data = array('User' => array (
				'username' => 'user4444',
				'email' => 'tran.thanh.son.gsc$$gmail.com',
				'password' => 'e20942c3cff85f77346d865785b5763363292794',
				'password_confirm' => '123456',
				'firstname' => 'nick',
				'lastname' => 'bamby'
			)
		);
		$this->Users->register();
		$this->assertTrue(isset($this->Users->User->validationErrors['email']));
		
		// Assert validation with password and confirm password
		$this->Users->data = array('User' => array (
				'username' => 'user4444',
				'email' => 'tran.thanh.son.gsc@gmail.com',
				'password' => '',
				'password_confirm' => '123456',
				'firstname' => 'nick',
				'lastname' => 'bamby'
			)
		);
		$this->Users->register();
		$this->assertEqual($this->Users->Session->read('Message.flash.message'), 'password and confirm password is not match');
	}

	function testActive() {
		$this->Users->params = Router::parse('/users/active');
		$this->Users->active('user2222');
		$user = $this->Users->User->findById(2);
		$this->assertEqual($user['User']['active'], 1);
	}
	
	function testLogin() {		
		$this->Users->params = Router::parse('/users/login');
		// Assert login successfully
		$this->Users->data = array('User' => array (
				'username' => 'user1111',
				'password' => '123456'
			)
		);
		$this->Users->login();
		$this->assertEqual($this->Users->Session->read('Message.flash.message'), 'Login successfully!');
		$this->assertEqual($this->Users->redirectUrl, '../posts');
		
		// Assert login fail (usernme or password is not true)
		$data = array('User' => array (
				'username' => 'user3333',
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
				'username' => 'user2222',
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
				'username' => 'user1111',
		));
		$this->assertEqual($this->Users->Session->read('Auth.User.id'), 1);
		$this->assertEqual($this->Users->Session->read('Auth.User.username'), 'user1111');
		
		$this->Users->params = Router::parse('/users/logout');
		$this->Users->logout();
		$this->assertEqual($this->Users->Session->read('Auth.User.id'), null);
		$this->assertEqual($this->Users->Session->read('Auth.User.username'), null);
		$this->assertEqual($this->Users->redirectUrl, '/users/login');
	}
}
?>