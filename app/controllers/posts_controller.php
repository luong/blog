<?php
class PostsController extends AppController {
    var $name = 'Posts';
    var $components = array('Session');
    var $paginate = array();
    
    function index() {
    	$conditions = array();
    	if(isset($this->passedArgs['Post.keyword'])) {
            $keyword = $this->passedArgs['Post.keyword'];
            $conditions[] = array (
    				'OR' => array(
    					"id"=> $keyword,
    					'title LIKE' => '%'.$keyword.'%'
    				) 
    		);
            $data['Post']['keyword'] = $keyword; 
        }
        //Limit and Order By
        $this->paginate= array(
        		'limit' => 4,
        		'order' => array('Post.id' => 'asc'),
        );
        $this->set("posts",$this->paginate("Post",$conditions));
    }

	function search() {
        // the page we will redirect to
        $url['action'] = 'index';     
        // build a URL will all the search elements in it
        foreach ($this->data as $k=>$v){ 
            foreach ($v as $kk=>$vv){ 
                $url[$k.'.'.$kk]=$vv; 
            } 
        }
        // redirect the user to the url
        $this->redirect($url, null, true);
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