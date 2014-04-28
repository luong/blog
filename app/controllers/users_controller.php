<?php
class UsersController extends AppController {
    var $name = 'Users';
    var $uses = array('User', 'Admin', 'Post');
    var $components = array('Session', 'Email', 'Auth');
    
    function beforeFilter(){
    	parent::beforeFilter();
    	$this->Auth->allow(array('register', 'active', 'admin_logout', 'admin_index', 
    					'admin_manageUsers', 'admin_delete', 'admin_edit'));
    }
  
    function register() {
    	if ($this->data) {
    		$tokenHash = sha1($this->data['User']['email'].rand(0,100));
    		$this->data['User']['token_hash'] = $tokenHash;
    		$this->data['User']['password'] = $this->Auth->password($this->data['User']['tmp_password']);
    		if ($this->User->save($this->data)) {
    			$email = $this->data['User']['email'];
    			$urlActive = Configure::read('app.url').str_replace('register', 'active/', Router::url()).$tokenHash.'/'.$this->User->id;
    			// send email which contain register info and active url
    			$this->email($this->data['User']['email'], $urlActive);
    			$this->Session->setFlash('Register successfully.');
    			$this->redirect(array('action' => 'login'));
    		}			    
		}
    }
    
    function active($tokenHash, $id){
    	if (!empty($tokenHash) && !empty($id)) {
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
    			else {
    				$this->Session->setFlash('Your registration failed please try again');
    				$this->redirect(array('action' => 'register'));
    			}
    		}
    		else {
    			$this->Session->setFlash('Token has alredy been used');
    			$this->redirect(array('action' => 'register'));
    		}
    	}    
    	else {
    		$this->Session->setFlash('Token corrupted. Please re-register');
    		$this->redirect(array('action' => 'register'));
    	}
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
    
    function admin_login() {
    	$this->layout = 'admin';
    	if(!empty($this->data)) {
    		$this->Admin->set($this->data);
    		if($this->Admin->validates()) {
    			$dataLogin = $this->Admin->find('first', array('conditions' => array(
    					'username' => $this->data['Admin']['username'],
    					'password' => $this->data['Admin']['password']
    				))
    			);
    			if($dataLogin != null) {
    				$this->Session->write('admin.username', $dataLogin['Admin']['username']);
    				$this->redirect(array('action' => 'index'));
    			}
    		}
    	}
    }
    
    function admin_logout() {
    	$this->Session->delete('admin.username');
    	$this->redirect(array('action' => 'admin_login'));
    }
    
    function admin_index() {
    	$this->layout = 'admin';
    	if($this->Session->read('admin.username') == null)
    		$this->redirect(array('action' => 'admin_login'));    		
    }
    
    function admin_manageUsers() {
    	$this->layout = 'admin';
    	if($this->Session->read('admin.username') != null) {
	    	$conditions = array();
	    	$keyword = '';
	    	if (!empty($this->params['named']['keyword'])) {
	    		$keyword = $this->params['named']['keyword'];
	    		$conditions[] = array (
	    				'OR' => array(
	    						'User.email LIKE' => '%' . $keyword . '%',
	    						'User.firstname LIKE' => '%' . $keyword . '%',
	    						'User.lastname LIKE' => '%' . $keyword . '%'
	    				)
	    		);
	    	}
	    	//Limit and Order By
	    	$this->paginate= array(
	    			'limit' => 4,
	    			'order' => array('User.id' => 'asc'),
	    			'recursive' => -1
	    	);
	    	$this->data['User']['keyword'] = $keyword;
	    	$this->set('users', $this->paginate('User', $conditions));
    	}
    	else
    		$this->redirect(array('action' => 'admin_login'));
    }
    
    function admin_delete($id) {
    	if($this->Session->read('admin.username') != null) {
    		if ($this->User->delete($id)) {
    			// delete all posts of user
    			$this->Post->deleteAll(array(
    					'Post.user_id' => $id
    			), true);
    			$this->Session->setFlash('The User with id: ' . $id . ' has been deleted.');	
    		}
    		$this->redirect(array('action' => 'admin_manageUsers'));
    	}
    	else
    		$this->redirect(array('action' => 'admin_login'));
    }
    
    function admin_edit($id) {
    	if($this->Session->read('admin.username') != null) {
    		$this->User->id = $id;
    		if (empty($this->data)) {
    			$this->User->recursive = -1;
    			$this->data = $this->User->find('first', array('conditions' => array('id' => $id),
    					'fields' => array('id', 'email', 'firstname', 'lastname')
    				)
    			);
    		} else {
    			$this->User->set($this->data);
    			if ($this->User->validates()) {
    				$this->User->saveAll($this->data);
    				$this->Session->setFlash('User has been updated.');
    				$this->redirect(array('action' => 'admin_manageUsers'));
    			}
    		}
    	}
    	else
    		$this->redirect(array('action' => 'admin_login'));
    }
 
}

?>