<?php

class User extends AppModel {
    var $name = 'User';
    var $useTable = "users";
   /*
    var $hasMany = array(
    		'Post' => array(
    				'className' => 'Post',
    				'foreignKey' => 'user_id'
    ));
    */
    var $validate = array(
    		'username' => array(
				        'rule1' => array(
				        		'rule' => 'alphaNumeric',
				        		'message' => 'Username only contains numbers and letters'
				        ),
	    				'rule2' => array(
	    						'rule' => 'notEmpty',
	    						'message' => 'Username can not empty'
	    				),
				        'rule3' => array(
				            	'rule' => array('maxLength', 20),
				            	'message' => 'Username can not be over 20 characters'
				        )
			),
    		'password' => array(
				        'rule4' => array(
				        		'rule' => 'alphaNumeric',
				        		'message' => 'Password only contains numbers and letters'
				        ),
	    				'rule5' => array(
	    						'rule' => 'notEmpty',
	    						'message' => 'Password can not empty'
	    				)
			),
    		'email' => array(
	    				'rule7' => array(
	    						'rule' => 'email',
	    						'message' => 'Email is invalid'
	    				),
	    				'rule8' => array(
	    						'rule' => 'notEmpty',
	    						'message' => 'Email can not empty'
	    				)
    		)
    );
    
    function activeUser($username) {
    	return $this->query('update users set active = 1 where username = "'.$username.'"');
    }

    function hashPasswords($data) {
    	if (isset($data['User']['password'])) {
    		$data['User']['password'] = md5($data['User']['password']);
    		return $data;
    	}
    	return $data;
    }
    
}

?>