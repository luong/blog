<?php
App::import('Controller', 'Posts');

class TestPostsController extends PostsController {
    var $name = 'Posts';
    
    function redirect($url, $status = null, $exit = true) {
        $this->redirectUrl = $url;
    }
}

class PostsControllerTest extends CakeTestCase {
	var $fixtures = array('app.post');
	
	function startTest() {
		$this->Posts = new TestPostsController();
		$this->Posts->constructClasses();
		$this->Posts->Component->initialize($this->Posts);
	}

	function endTest() {
		unset($this->Posts);
		ClassRegistry::flush();
	}
	
	function testIndex() {
		$result = $this->testAction ('/posts', array(
				'return' => 'vars' 
		) );
		
		// Check number of posts in index
		$this->assertEqual (count($result['posts']), 10);
		$result = Set::extract($result, 'posts.{n}.Post.id');
		// Check match array of id of index and expected array of id
		$this->assertEqual ($result, array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10) );
	}
	
	function testView() {
		// Test post which have id = 3  
		$result = $this->testAction ('/posts/view/3', array(
				'return' => 'vars' 
		) );
		$this->assertEqual($result['post']['Post']['title'], 'Third Article');
		$this->assertEqual($result['post']['Post']['body'], 'Third Article Body');
		
		// Test post which have id = 5
		$result = $this->testAction ( '/posts/view/5', array (
				'return' => 'vars'
		) );
		$this->assertEqual($result['post']['Post']['title'], 'fifth Article');
		$this->assertEqual($result['post']['Post']['body'], 'fifth Article Body');
	}
	
	function testAdd() {
		$this->Posts->data = array (
				'Post' => array (
						'id' => 11,
						'title' => 'eleventh Article',
						'body' => 'eleventh Article Body',
						'created' => '2007-03-18 10:43:23',
						'modified' => '2007-03-18 10:45:31' 
				)
		);

		$this->Posts->params = Router::parse('/posts/add');
		$this->Posts->beforeFilter();
		$this->Posts->Component->startup($this->Posts);
		$this->Posts->add();
		
		//assert the record was changed
		$result = $this->Posts->Post->find('all');
		$this->assertEqual(count($result), 11);
		$this->assertEqual($result[10]['Post']['title'], 'eleventh Article');
		$this->assertEqual($result[10]['Post']['body'], 'eleventh Article Body');
	}
		
	function testDelete() {
		$result = $this->testAction ('/posts', array(
				'return' => 'vars'
		) );
		// check number of post before delete
		$this->assertEqual (count($result['posts']), 10);
		
		$this->Posts->params = Router::parse('/posts/delete');
		$this->Posts->beforeFilter();
		$this->Posts->Component->startup($this->Posts);
		$this->Posts->delete(10);
		// check number of post after delete
		$result = $this->Posts->Post->find('all');
		$this->assertEqual(count($result), 9);
	}

	function testEdit() {
		$this->Posts->data = array(
				'Post' => array(
						'id' => 5,
						'title' => 'edit title 5',
						'body' => 'edit body 5' 
				) 
		);
		
		$this->Posts->params = Router::parse('/posts/edit');
		$this->Posts->beforeFilter();
		$this->Posts->Component->startup($this->Posts);
		$this->Posts->edit();
		
		//assert the record was changed
		$result = $this->Posts->Post->read(null,5);
		$this->assertEqual($result['Post']['title'], 'edit title 5');
		$this->assertEqual($result['Post']['body'], 'edit body 5');
	}
}
?>