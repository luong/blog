<?php
class AppController extends Controller {
	var $components = array ('Auth');
	
	function beforeFilter(){   
		$this->Auth->userModel = 'User';
		$this->Auth->fields = array('username' => 'email', 'password' => 'password');
		$this->Auth->userScope = array('User.active' => 1);
        $this->Auth->loginAction = array('controller'=>'users','action'=>'login');
        $this->Auth->loginRedirect = array('controller'=>'posts','action'=>'index');
        $this->Auth->loginError = 'Failed to login';
        $this->Auth->authError = 'Access denied';       
    }
}