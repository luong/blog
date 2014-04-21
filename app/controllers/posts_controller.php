<?php
class PostsController extends AppController {
    var $name = 'Posts';
    var $uses = array('Post','Tag', 'PostTag');
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
    	// get all tags in tags table to use for view
    	$this->set('tags', $this->Tag->find('all'));
    	
        if (!empty($this->data)) {
            if ($this->Post->save($this->data)) {
            	// get selected tag from add screen
            	$tags = $this->data['Post']['tag'];
            	$tag_id = '';
            	// change tags from array to string
            	for($i=0;$i<count($tags);$i++) {
            		$tag_id .= $tags[$i];
            		if($i != count($tags)-1)
            			$tag_id .= ',';
            	}
            	// Save data into post_tag table
            	$this->PostTag->save(array(
        			'post_id' => $this->Post->id,
            		'tag_id' => $tag_id
            	));
            	
                $this->Session->setFlash('Your post has been saved.');
                $this->redirect(array('action' => 'index'));
            }
        }
    }
    
    function delete($id) {
    	if ($this->Post->delete($id)) {
    		// get id of post_Tag
    		$pot_tag_id = $this->PostTag->find('first', array(
    				'conditions' => array('post_id' => $id),
    				'fields' => array('id')
    		));
    		// delete field which has post_id=$id in post_tag table
    		$this->PostTag->delete($pot_tag_id['PostTag']['id']);
    		$this->Session->setFlash('The post with id: ' . $id . ' has been deleted.');
    		$this->redirect(array('action' => 'index'));
    	}
    }
    
    function edit($id = null) {
    	// get all tags in tags table to use for view
    	$this->set('tags', $this->Tag->find('all'));
    	// get all selected tags
    	$this->set('selectedTags', $this->PostTag->find('first', array('conditions' => array('post_id' => $id))));
    	
    	$this->Post->id = $id;
    	if (empty($this->data)) {
    		$this->data = $this->Post->read();
    	} else {
    		if ($this->Post->save($this->data)) {
    			// get tags data in edit screen
    			$tags = $this->data['Post']['tag'];
    			$tag_id = '';
    			// change tags from array to string
    			for($i=0;$i<count($tags);$i++) {
    				$tag_id .= $tags[$i];
    				if($i != count($tags)-1)
    					$tag_id .= ',';
    			}
    			// get id of post_Tag
    			$pot_tag_id = $this->PostTag->find('first', array(
    								'conditions' => array('post_id' => $id),
    								'fields' => array('id')
    								));
    			// Save data into post_tag table
    			$this->PostTag->save(array(
    					'id' => $pot_tag_id['PostTag']['id'],
    					'post_id' => $this->Post->id,
    					'tag_id' => $tag_id
    			));
    			$this->Session->setFlash('Your post has been updated.');
    			$this->redirect(array('action' => 'index'));
    		}
    	}
    }
}
?>