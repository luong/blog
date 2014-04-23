<?php
class PostsController extends AppController {
    var $name = 'Posts';
    var $uses = array('Post', 'Tag', 'PostsTag');
    var $components = array('Session');
    var $paginate = array();
    
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
        $this->Post->id = $id;
        $this->set('post', $this->Post->read());
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
    	if ($this->Post->delete($id)) {
    		$this->Session->setFlash('The post with id: ' . $id . ' has been deleted.');
    		$this->redirect(array('action' => 'index'));
    	}
    }
    
    function edit($id = null) {
    	// get all tags in tags table to use for view
    	$this->set('tags', $this->Tag->find('all'));

    	$this->Post->id = $id;
    	if (empty($this->data)) {
    		$this->data = $this->Post->read();
    	} else {
    		if ($this->Post->saveAll($this->data)) {
    			$this->Session->setFlash('Your post has been updated.');
    			$this->redirect(array('action' => 'index'));
    		}
    	}
    }
}
?>