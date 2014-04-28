<?php

class Admin extends AppModel {
    var $name = 'Admin';
    var $actsAs = array('Containable');
    
    var $validate = array(
    		'username' => array(
    				'untrueUsername' => array(
    						'rule' => 'checkUsername',
    						'message' => 'Username is wrong.'
    				),
    				'usernameEmpty' => array(
    						'rule' => 'notEmpty',
    						'message' => 'Username is empty'
    				)
    		),
    		'password' => array(
    				'untruePassword' => array(
    						'rule' => array('untruePassword', 'username'),
    						'message' => 'Password is wrong.'
    				),
    				'passwordEmpty' => array(
    						'rule' => 'notEmpty',
    						'message' => 'Password is empty'
    				)
    		)
    );
    
    function checkUsername($data) {
    	if($this->hasAny(array('username' => $data['username'])))
    		return true;
    	return false;
    }
    
    function untruePassword($data, $username) {
    	if($this->hasAny(array('username' => $this->data[$this->name][$username], 'password' => $data['password'])) != null)
    		return true;
    	return false;
    }
}

?>