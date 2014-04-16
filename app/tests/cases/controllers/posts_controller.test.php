<?php
App::import ( 'Controller', 'Posts' );

class PostsControllerTest extends CakeTestCase {
	
	function testIndex() {
		$result = $this->testAction ( '/posts', array (
				'fixturize' => true,
				'return' => 'view' 
		) );
		debug ( $result );
	}
	
	function testViewPost() {
		$result = $this->testAction ( '/posts/view/3', array (
				'fixturize' => true,
				'return' => 'view' 
		) );
		debug ( $result );
	}
	
	function testAddPost() {
		$data = array (
				'Post' => array (
						'id' => 5,
						'title' => 'Best article Evar!',
						'body' => 'some text' 
				) 
		);
		$results = $this->testAction ( '/posts/add', array (
				'fixturize' => true,
				'data' => $data,
				'method' => 'post' 
		) );
		debug ( $results );
	}
	
	function testDeletePost() {
		$result = $this->testAction ( '/posts/delete/3', array (
				'fixturize' => true,
				'return' => 'vars' 
		) );
		debug ( $result );
	}
	
	function testEditPost() {
		$data = array (
				'Post' => array (
						'id' => 3,
						'title' => 'edit title!',
						'body' => 'edit body' 
				) 
		);
		$results = $this->testAction ( '/posts/edit', array (
				'fixturize' => true,
				'data' => $data,
				'method' => 'post' 
		) );
		debug ( $results );
	}
	
}
?>