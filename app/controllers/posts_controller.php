<?php
class PostsController extends AppController {
    var $name = 'Posts';
    var $uses = array('Post', 'Tag', 'PostsTag');
    var $components = array('Session');
    var $paginate = array();
    
    function beforeFilter(){
    	parent::beforeFilter();
    	$this->Auth->allow(array('admin_managePosts'));
    }

    function index() {
    	$conditions[] = array('Post.user_id' => $this->Session->read('Auth.User.id'));
    	$keyword = '';
    	if (!empty($this->params['named']['keyword'])) {
            $keyword = $this->params['named']['keyword'];
            $conditions[] = array (
    			'OR' => array(
    				'Post.title LIKE' => '%' . $keyword . '%',
    				'Post.body LIKE' => '%' . $keyword . '%'
    			) 
    		);
        }
        //Limit and Order By
        $this->paginate= array(
        	'limit' => 4,
        	'order' => array('Post.id' => 'asc'),
        	'recursive' => -1
        );
        $this->data['Post']['keyword'] = $keyword;
        $this->set('posts', $this->paginate('Post', $conditions));
    }

    function view($id) {
        $this->Post->recursive = -1;
        $post = $this->Post->find('first', array('conditions' => array('id' => $id),
        										'fields' => array('title', 'body', 'created')
        	)
        );
        $this->set('post', $post);
    }

    function add() {
    	// get all tags in tags table to use for view
    	$this->set('tags', $this->Tag->find('all'));	
        if (!empty($this->data)) {
            if ($this->Post->saveAll($this->data)) {
                $this->Session->setFlash('Your post has been saved.');
                $this->redirect(array('action' => 'index'));
            }
        }
    }
    
    function delete($id) {
    	$data = $this->Post->findById($id);
    	if($data['Post']['user_id'] == $this->Session->read('Auth.User.id')) {
	    	if ($this->Post->delete($id, true)) {
	    		$this->Session->setFlash('The post with id: ' . $id . ' has been deleted.');
	    	}
    	} 
    	else 
    		$this->Session->setFlash('Delete fail.');
    	$this->redirect(array('action' => 'index'));
    }
    
    function edit($id = null) {
    	$data = $this->Post->findById($id);
    	if($data['Post']['user_id'] == $this->Session->read('Auth.User.id')) {
	    	// get all tags in tags table to use for view
	    	$this->set('tags', $this->Tag->find('all'));
	
	    	$this->Post->id = $id;
	    	if (empty($this->data)) {
	    		$this->data = $this->Post->find('first', array('conditions' => array('id' => $id),
	    							'fields' => array('title', 'body'),
	    							'contain' => array('PostsTag' => array('tag_id')
	    				)				
	    			)
	    		);
	    	} else {
	    		$this->Post->set($this->data);
	    		if ($this->Post->validates()) {
	    			$this->PostsTag->deleteAll(array(
							"PostsTag.post_id" => $id
					));
	    			$this->Post->saveAll($this->data);
	    			$this->Session->setFlash('Your post has been updated.');
	    			$this->redirect(array('action' => 'index'));
	    		}
	    	}
    	}
    	else {
    		$this->Session->setFlash('Update fail.');
    		$this->redirect(array('action' => 'index'));
    	}
    }
    
    function admin_managePosts() {
    	$this->layout = 'admin';
    	if($this->Session->read('admin.username') != null) {
    		$conditions = array();
    		$keyword = '';
    		if (!empty($this->params['named']['keyword'])) {
    			$keyword = $this->params['named']['keyword'];
    			$conditions[] = array (
    					'OR' => array(
    							'Post.title LIKE' => '%' . $keyword . '%',
    							'Post.body LIKE' => '%' . $keyword . '%'
    					)
    			);
    		}
    		//Limit and Order By
    		$this->paginate= array(
    				'limit' => 4,
    				'order' => array('Post.id' => 'asc'),
    				'recursive' => -1
    		);
    		$this->data['Post']['keyword'] = $keyword;
    		$this->set('posts', $this->paginate('Post', $conditions));
    	}
    	else
    		$this->redirect(array('controller' => 'Users', 'action' => 'admin_login'));
    }
    
    function admin_delete($id) {
    	if($this->Session->read('admin.username') != null) {
    		if ($this->Post->delete($id, true)) {
    			$this->Session->setFlash('The post with id: ' . $id . ' has been deleted.');
    			$this->redirect(array('action' => 'admin_managePosts'));
    		}
    	}
    	else
    		$this->redirect(array('controller' => 'Users', 'action' => 'admin_login'));
    }
    
    function admin_edit($id = null) {
    	if($this->Session->read('admin.username') != null) {
    		// get all tags in tags table to use for view
    		$this->set('tags', $this->Tag->find('all'));
    
    		$this->Post->id = $id;
    		if (empty($this->data)) {
    			$this->data = $this->Post->find('first', array('conditions' => array('id' => $id),
    					'fields' => array('title', 'body'),
    					'contain' => array('PostsTag' => array('tag_id')
    					)
    			)
    			);
    		} else {
    			$this->Post->set($this->data);
    			if ($this->Post->validates()) {
    				$this->PostsTag->deleteAll(array(
    						"PostsTag.post_id" => $id
    				));
    				$this->Post->saveAll($this->data);
    				$this->Session->setFlash('Your post has been updated.');
    				$this->redirect(array('action' => 'admin_managePosts'));
    			}
    		}
    	}
    	else {
    		$this->redirect(array('controller' => 'Users', 'action' => 'admin_login'));
    	}
    }
}
?>