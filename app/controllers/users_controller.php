<?php
class UsersController extends AppController {
    var $name = 'Users';
    var $uses = array('User');
    var $components = array('Session', 'Email', 'Auth');
    
    function beforeFilter(){
    	parent::beforeFilter();
    	$this->Auth->allow(array('register', 'active'));
    }
  
    function register() {
    	if ($this->data) {
    		$tokenHash = sha1($this->data['User']['email'].rand(0,100));
    		$this->data['User']['token_hash'] = $tokenHash;
    		$this->data['User']['password'] = $this->Auth->password($this->data['User']['tmp_password']);
    		if ($this->User->save($this->data)) {
    			$email = $this->data['User']['email'];
    			$urlActive = 'http://test/'.str_replace('register', 'active/h:', Router::url()).$tokenHash.'/i:'.$this->User->id;
    			// send email which contain register info and active url
    			$this->email($this->data['User']['email'], $urlActive);
    			$this->Session->setFlash('Register successfully.');
    			$this->redirect(array('action' => 'login'));
    		}			    
		}
    }
    
    function active($username){
    	if (!empty($this->passedArgs['h']) && !empty($this->passedArgs['i'])) {
    		$id = $this->passedArgs['i'];
    		$tokenHash = $this->passedArgs['h'];
    		$results = $this->User->findById($id);
    		if ($results['User']['active'] == 0) {
    			if ($results['User']['token_hash'] == $tokenHash) {
    				$this->User->save(array(
    						'id' => $results['User']['id'],
    						'active' => 1
    					)
    				);
    				$this->Session->setFlash('Your registration is complete');
    				$this->redirect(array('action' => 'login'));
    			}
    			else 
    				$this->Session->setFlash('Your registration failed please try again');
    		}
    		else 
    			$this->Session->setFlash('Token has alredy been used');
    	}    
    	else 
    		$this->Session->setFlash('Token corrupted. Please re-register');
    	$this->redirect(array('action' => 'register'));
    }

	function login() {
		
    }
    
    function logout() {
    	$this->redirect($this->Auth->logout());
    }
    
    function email($mailTo, $urlActive){
    	$this->Email->SMTPAuth = true;
    	$this->Email->SMTPSecure = 'tls';
    	$this->Email->charset  = 'UTF-8';
    	$this->Email->headerCharset = "UTF-8";
    	$this->Email->to = $mailTo;
    	$this->Email->from = Configure::read('app.username');
    	$this->Email->subject = 'Active your acount!';
    	$this->Email->laout = 'default';
    	$this->Email->template = 'register';
    	$this->Email->sendAs = 'html';
    		
    	$this->Email->smtpOptions = array(
    			'port' => Configure::read('app.port'),
    			'timeout'=>'30',
    			'host' => Configure::read('app.host'),
    			'username' => Configure::read('app.username'),
    			'password' => Configure::read('app.password'),
    	);

    	$this->set("urlActive", $urlActive);
    	$this->Email->delivery = 'smtp';
    	if($this->Email->Send()) {
    
    	} else {
    		echo $this->Email->smtpError;
    	}
    }
 
}

?>