<?php
class PostsController extends AppController {
    var $name = 'Posts';
    var $components = array('Session');
    var $paginate = array();
    
    function index() {
    	$keyword = $this->data["Post"]["keyword"];
    	if($keyword == "") {
    		$this->paginate = array(
		    		'fields' => array('Post.id', 'Post.title', 'Post.Created'),
		    		'limit' => 4,
		    		'order' => array(
		    				'Post.id' => 'asc'
		    		)
		    );
    	}
    	else {
    		$this->paginate = array(
    				'fields' => array('Post.id', 'Post.title', 'Post.Created'),
    				'conditions' => array (
					        'OR' => array(
					            "id"=> $keyword,
					            'title LIKE' => '%'.$keyword.'%'
					)
    			)
    		);
    	}	
    	 	
    	$data = $this->paginate("Post");
    	$this->set('posts', $data);
    }

    function view($id) {
        $this->Post->id = $id;
        $this->set('post', $this->Post->read());
    }

    function add() {
        if (!empty($this->data)) {
            if ($this->Post->save($this->data)) {
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
    	$this->Post->id = $id;
    	if (empty($this->data)) {
    		$this->data = $this->Post->read();
    	} else {
    		if ($this->Post->save($this->data)) {
    			$this->Session->setFlash('Your post has been updated.');
    			$this->redirect(array('action' => 'index'));
    		}
    	}
    }
}
?>