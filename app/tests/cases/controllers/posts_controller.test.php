<?php
App::import('Controller', 'Posts');

class TestPostsController extends PostsController {
    var $name = 'Posts';
 
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

class PostsControllerTest extends CakeTestCase {
	var $fixtures = array('app.post');
	
	function startTest() {
		$this->Posts = new TestPostsController();
		$this->Posts->constructClasses();
		$this->Posts->beforeFilter();
		$this->Posts->Component->initialize($this->Posts);
	}

	function endTest() {
		$this->Posts->Session->destroy();
		unset($this->Posts);
		ClassRegistry::flush();
	}
	
	function testIndex() {
		$result = $this->testAction ('/posts', array(
			'return' => 'vars' 
		));
		// Assert post count
		$this->assertEqual(count($result['posts']), 4);
		$postIds = Set::extract('/Post/id', $result['posts']);
		// Assert post ids
		$this->assertEqual($postIds, array(1, 2, 3, 4));
		// Check data in page 2
		$result = $this->testAction ('/posts/index/page:2', array(
				'return' => 'vars'
		));
		// Assert post count
		$this->assertEqual(count($result['posts']), 4);
		$postIds = Set::extract('/Post/id', $result['posts']);
		// Assert post ids
		$this->assertEqual($postIds, array(5, 6, 7, 8));
		
		// Check data in page 3
		$result = $this->testAction ('/posts/index/page:3', array(
				'return' => 'vars'
		));
		// Assert post count
		$this->assertEqual(count($result['posts']), 2);
		$postIds = Set::extract('/Post/id', $result['posts']);
		// Assert post ids
		$this->assertEqual($postIds, array(9, 10));
		
		// check search data with id = 1
		$data = array('Post' => array (
				'keyword' => 1
		));
		$result = $this->testAction ('/posts', array (
				'return' => 'vars',
				'data' => $data,
				'method' => 'post'
		));
		$this->assertEqual(count($result['posts']), 1);
		$this->assertEqual($result['posts'][0]['Post']['title'], "First Article");
		
		// check search data with title like %seventh%
		$data = array('Post' => array (
				'keyword' => 'seventh'
		));
		$result = $this->testAction ('/posts', array (
				'return' => 'vars',
				'data' => $data,
				'method' => 'post'
		));
		$this->assertEqual(count($result['posts']), 1);
		$this->assertEqual($result['posts'][0]['Post']['id'], 7);
		$this->assertEqual($result['posts'][0]['Post']['title'], "seventh Article");
	}
	
	function testView() {
		// Assert the third post
		$result = $this->testAction('/posts/view/3', array(
			'return' => 'vars' 
		));
		$this->assertEqual($result['post']['Post']['title'], 'Third Article');
		$this->assertEqual($result['post']['Post']['body'], 'Third Article Body');
		// Assert the fifth post
		$result = $this->testAction ('/posts/view/5', array (
			'return' => 'vars'
		));
		$this->assertEqual($result['post']['Post']['title'], 'fifth Article');
		$this->assertEqual($result['post']['Post']['body'], 'fifth Article Body');
		// Assert invalid post
		$result = $this->testAction ('/posts/view/101', array (
			'return' => 'vars'
		));
		$this->assertFalse($result['post']);
	}
	
	
	function testAdd() {
		$this->Posts->params = Router::parse('/posts/add');
		// Assert successfully added
		$this->Posts->data = array('Post' => array (
			'id' => 11,
			'title' => 'eleventh Article',
			'body' => 'eleventh Article Body',
			'created' => '2007-03-18 10:43:23',
			'modified' => '2007-03-18 10:45:31'
		));
		$this->Posts->add();
		$post = $this->Posts->Post->findById(11);
		$this->assertEqual($post['Post']['title'], 'eleventh Article');
		$this->assertEqual($post['Post']['body'], 'eleventh Article Body');
		$this->assertEqual($this->Posts->Session->read('Message.flash.message'), 'Your post has been saved.');
		$this->assertEqual($this->Posts->redirectUrl, array('action' => 'index'));
		// Assert validation with title required
		$this->Posts->data = array('Post' => array (
			'id' => 12,
			'title' => '',
			'body' => 'eleventh Article Body',
			'created' => '2007-03-18 10:43:23',
			'modified' => '2007-03-18 10:45:31'
		));
		$this->Posts->add();
		$this->assertTrue(isset($this->Posts->Post->validationErrors['title']));
		// Assert validation with body required
		$this->Posts->data = array('Post' => array (
			'id' => 12,
			'title' => 'eleventh Article',
			'body' => '',
			'created' => '2007-03-18 10:43:23',
			'modified' => '2007-03-18 10:45:31'
		));
		$this->Posts->add();
		$this->assertTrue(isset($this->Posts->Post->validationErrors['body']));
	}
	
	function testDelete() {
		$this->assertEqual($this->Posts->Post->find('count'), 10);
		$this->Posts->params = Router::parse('/posts/delete');
		// Assert failed case
		$this->Posts->delete(101);
		$this->assertEqual($this->Posts->Post->find('count'), 10);
		// Assert successful case
		$this->Posts->delete(10);
		$this->assertEqual($this->Posts->Post->find('count'), 9);
		$this->assertEqual($this->Posts->Session->read('Message.flash.message'), 'The post with id: 10 has been deleted.');
		$this->assertEqual($this->Posts->redirectUrl, array('action' => 'index'));
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
		$this->Posts->edit();
		// Assert successfully updated
		$post = $this->Posts->Post->read(null, 5);
		$this->assertEqual($post['Post']['title'], 'edit title 5');
		$this->assertEqual($post['Post']['body'], 'edit body 5');
		$this->assertEqual($this->Posts->Session->read('Message.flash.message'), 'Your post has been updated.');
		$this->assertEqual($this->Posts->redirectUrl, array('action' => 'index'));
		// Assert validation with title required
		$this->Posts->data = array('Post' => array (
			'id' => 5,
			'title' => '',
			'body' => 'edit body 5' 
		));
		$this->Posts->edit();
		$this->assertTrue(isset($this->Posts->Post->validationErrors['title']));
		// Assert validation with body required
		$this->Posts->data = array(
			'Post' => array(
				'id' => 5,
				'title' => 'edit title 5',
				'body' => '' 
			) 
		);
		$this->Posts->edit();
		$this->assertTrue(isset($this->Posts->Post->validationErrors['body']));
	}
}
?>