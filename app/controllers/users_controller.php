<?php
class UsersController extends AppController {
    var $name = 'Users';
    var $uses = array('User');
    var $components = array('Session', 'Email', 'Auth');
    
    function beforeFilter(){
    	parent::beforeFilter();
    	$this->Auth->allow(array('register', 'active'));
    	$this->Auth->autoRedirect = false;
    }
  
    function register() {
    	if ($this->data) {
    		$existsUsername = $this->User->find('first', array('conditions' => array(
    				'username' => $this->data['User']['username']
    		)));
    		if($existsUsername == null){
    			if ($this->data['User']['password'] == $this->Auth->password($this->data['User']['password_confirm'])) {
	    			if ($this->User->save($this->data)) {
		    			$username = $this->data['User']['username'];
		    			$urlActive = 'http://test/users/active/'.$username;
		    			// send email which contain register info and active url
		    			$this->email($this->data['User']['email'], $this->data['User'], $urlActive);
		    			$this->Session->setFlash('Register successfully.');
		    			$this->redirect(array('action' => 'login'));
		    		}	
    			}     			
	    		else
	    			$this->Session->setFlash('password and confirm password is not match');
    		}
	    	else
	    		$this->Session->setFlash('This username is exists.');
    	}
    }
    
    function active($username){
    	if($username != null && $this->User->find('first', array('conditions' => array('username' => $username))) != null){
    		$this->User->activeUser($username);
    	}
    	$this->Session->setFlash('active successfully.');
    	$this->redirect(array('action' => 'login'));
    }

	function login() {
		if(isset($this->data)){
	        $dataLogIn = $this->User->find('first', array('conditions' => array(
	        				'username' => $this->data['User']['username'],
	        				'password' => $this->data['User']['password']
	        		)));
	        if($dataLogIn != null) {
	        	if($dataLogIn['User']['active'] == 0)
	        		$this->Session->setFlash('Username has not been actived yet');
	        	else {
	        		$this->Session->setFlash('Login successfully!');
	        		$this->redirect('../posts', null, true);
	        	}	
	        }
		}
    }
    
    function logout() {
    	$this->redirect($this->Auth->logout());
    }
    
    function email($mailTo, $info, $urlActive){
    	$this->Email->SMTPAuth = true;
    	$this->Email->SMTPSecure = 'tls';
    	$this->Email->charset  = 'UTF-8';
    	$this->Email->headerCharset = "UTF-8";
    	$this->Email->to = $mailTo;
    	$this->Email->from = 'sontt10279271@gmail.com';
    	$this->Email->subject = 'Active your acount!';
    	$this->Email->template = 'register';
    	$this->Email->sendAs = 'text';
    		
    	$this->Email->smtpOptions = array(
    			'port' => 465,
    			'timeout'=>'30',
    			'host' => 'ssl://smtp.gmail.com',
    			'username' => 'sontt10279271@gmail.com',
    			'password' => '10279271',
    	);
    
    	$this->set("info", $info);
    	$this->set("urlActive", $urlActive);
    	$this->Email->delivery = 'smtp';
    	if($this->Email->Send()) {
    
    	} else {
    		echo $this->Email->smtpError;
    	}
    }
 
}

?>