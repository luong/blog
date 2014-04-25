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
	var $fixtures = array('app.post', 'app.tag', 'app.posts_tag', 'app.user');
	var $components = array('Session');

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
		// write session for authentication
		$this->Posts->Session->write('Auth.User', array(
				'id' => 1,
				'username' => EMAIL_FOR_AUTH,
		));
		
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
		$this->assertEqual(count($result['posts']), 1);
		$postIds = Set::extract('/Post/id', $result['posts']);
		// Assert post ids
		$this->assertEqual($postIds, array(5));
		
		// check search data with keyword = first
		$result = $this->testAction ('/posts/index/keyword:First', array (
				'return' => 'vars'
		));
		$this->assertEqual(count($result['posts']), 1);
		$this->assertEqual($result['posts'][0]['Post']['id'], 1);
		$this->assertEqual($result['posts'][0]['Post']['title'], 'First Article');
		$this->assertEqual($result['posts'][0]['Post']['body'], 'First Article Body');
	}

	function testView() {
		// write session for authentication
		$this->Posts->Session->write('Auth.User', array(
				'id' => 1,
				'username' => EMAIL_FOR_AUTH,
		));
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
		// write session for authentication
		$this->Posts->Session->write('Auth.User', array(
				'id' => 1,
				'username' => EMAIL_FOR_AUTH,
		));
		
		$this->Posts->params = Router::parse('/posts/add');
		// Assert successfully added
		$this->Posts->data = array('Post' => array (
				'id' => 11,
				'user_id' => 1,
				'title' => 'eleventh Article',
				'body' => 'eleventh Article Body',
				'created' => '2007-03-18 10:43:23',
				'modified' => '2007-03-18 10:45:31'
			),
			'PostsTag' => Array(
            		'0' => Array(
                    	'tag_id' => 1
                	),
					'1' => Array(
						'tag_id' => 2
					),
					'2' => Array(
						'tag_id' => 3
					)
        	)
		);
		$this->Posts->add();
		$post = $this->Posts->Post->findById(11);
		$this->assertEqual($post['Post']['title'], 'eleventh Article');
		$this->assertEqual($post['Post']['body'], 'eleventh Article Body');
		$this->assertEqual($post['Post']['user_id'], 1);
		// get tag_ids from posts_tags
		$tagIds = Set::extract($post, 'PostsTag.{n}.tag_id');
		$this->assertEqual(count($post['PostsTag']), 3);
		$this->assertEqual($tagIds, array(1, 2, 3));
		$this->assertEqual($this->Posts->Session->read('Message.flash.message'), 'Your post has been saved.');
		$this->assertEqual($this->Posts->redirectUrl, array('action' => 'index'));
		// Assert validation with title required
		$this->Posts->data = array('Post' => array (
				'id' => 12,
				'user_id' => 1,
				'title' => '',
				'body' => 'eleventh Article Body',
				'created' => '2007-03-18 10:43:23',
				'modified' => '2007-03-18 10:45:31'
			)
		);
		$this->Posts->add();
		$this->assertTrue(isset($this->Posts->Post->validationErrors['title']));
		// Assert validation with body required
		$this->Posts->data = array('Post' => array (
				'id' => 12,
				'user_id' => 1,
				'title' => 'eleventh Article',
				'body' => '',
				'created' => '2007-03-18 10:43:23',
				'modified' => '2007-03-18 10:45:31'
			)
		);
		$this->Posts->add();
		$this->assertTrue(isset($this->Posts->Post->validationErrors['body']));
	}
	
	function testDelete() {
		// write session for authentication
		$this->Posts->Session->write('Auth.User', array(
				'id' => 1,
				'username' => EMAIL_FOR_AUTH,
		));
		// check info of post which have id equal 1 before delete
		$this->assertEqual($this->Posts->Post->find('count', array('conditions' => array(
								'user_id' => 1
					))), 5);
		$this->assertEqual($this->Posts->PostsTag->find('count'), 5);
		
		$this->Posts->params = Router::parse('/posts/delete');
		// Assert failed case
		$this->Posts->delete(101);
		$this->assertEqual($this->Posts->Post->find('count', array('conditions' => array(
								'user_id' => 1
					))), 5);
		$this->assertEqual($this->Posts->PostsTag->find('count'), 5);
		// Assert successful case
		$this->Posts->delete(1);
		$this->assertEqual($this->Posts->Post->find('count', array('conditions' => array(
								'user_id' => 1
					))), 4);
		$this->assertEqual($this->Posts->PostsTag->find('count'), 2);
		$this->assertEqual($this->Posts->Session->read('Message.flash.message'), 'The post with id: 1 has been deleted.');
		$this->assertEqual($this->Posts->redirectUrl, array('action' => 'index'));
	}

	function testEdit() {
		// write session for authentication
		$this->Posts->Session->write('Auth.User', array(
				'id' => 1,
				'username' => EMAIL_FOR_AUTH,
		));
		// check info of post which have id equal 1 before edit
		$post = $this->Posts->Post->read(null, 1);
		$this->assertEqual($post['Post']['title'], 'First Article');
		$this->assertEqual($post['Post']['body'], 'First Article Body');
		$tagIds = Set::extract($post, 'PostsTag.{n}.tag_id');
		$this->assertEqual(count($post['PostsTag']), 3);
		$this->assertEqual($tagIds, array(1, 2, 3));
		
		$this->Posts->data = array(
			'Post' => array(
				'id' => 1,
				'title' => 'edit title 1',
				'body' => 'edit body 1'
			),
			'PostsTag' => Array(
            		'0' => Array(
                    	'tag_id' => 1
                	),
					'1' => Array(
						'tag_id' => 2
					),
					'2' => Array(
						'tag_id' => 5
					)
        	)
		);
		$this->Posts->params = Router::parse('/posts/edit');
		$this->Posts->edit(1);
		// Assert successfully updated
		$post = $this->Posts->Post->read(null, 1);
		$this->assertEqual($post['Post']['title'], 'edit title 1');
		$this->assertEqual($post['Post']['body'], 'edit body 1');
		// get tag_ids from posts_tags
		$tagIds = Set::extract($post, 'PostsTag.{n}.tag_id');
		$this->assertEqual(count($post['PostsTag']), 3);
		$this->assertEqual($tagIds, array(1, 2, 5));
		$this->assertEqual($this->Posts->Session->read('Message.flash.message'), 'Your post has been updated.');
		$this->assertEqual($this->Posts->redirectUrl, array('action' => 'index'));
		// Assert validation with title required
		$this->Posts->data = array('Post' => array (
				'id' => 5,
				'title' => '',
				'body' => 'edit body 5'
			)
		);
		$this->Posts->edit(5);
		$this->assertTrue(isset($this->Posts->Post->validationErrors['title']));
		// Assert validation with body required
		$this->Posts->data = array(
			'Post' => array(
				'id' => 5,
				'title' => 'edit title 5',
				'body' => ''
			)
		);
		$this->Posts->edit(5);
		$this->assertTrue(isset($this->Posts->Post->validationErrors['body']));
	}
}
?>