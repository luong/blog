<?php

class User extends AppModel {
    var $name = 'User';
    var $actsAs = array('Containable');

    var $hasMany = array(
    		'Post' => array(
    				'className' => 'Post',
    				'foreignKey' => 'user_id'
    ));

    var $validate = array(
    		'id' => array(),
    		'email' => array(
    				'emailInvalid' => array(
    						'rule' => 'email',
    						'message' => 'Email is empty or invalid'
    				),
    				'emailExists' => array(
    						'rule' => array('checkEmailExists', 'id'),
    						'message' => 'Email existed, please input other email.'
    				)
    		),
    		'tmp_password' => array(
	    			'passwordEmpty' => array(
	    					'rule' => 'notEmpty',
	    					'message' => 'Password is empty'
	    			)
			),
    		'password_confirm' => array(
    				'matchPassword' => array(
    						'rule' => array('matchPassword', 'tmp_password'),
    						'message' => 'Password confirmation does not match password.'
    				)
    		),
    		'firstname' => array(
    				'firstnameEmpty' => array(
	    					'rule' => 'notEmpty',
	    					'message' => 'First name is empty'
	    			)
    		),
    		'lastname' => array(
    				'lastnameEmpty' => array(
	    					'rule' => 'notEmpty',
	    					'message' => 'Last name is empty'
	    			)
    		)
    );
    
	function matchPassword($data, $password) {
	    return ($this->data[$this->name][$password] == $data['password_confirm']);
	}
	
	function checkEmailExists ($data, $id) {
		if(!isset($this->data[$this->name][$id]))
			if($this->hasAny(array('email' => $data['email'])))
				return false;
		return true;
	}

}

?>