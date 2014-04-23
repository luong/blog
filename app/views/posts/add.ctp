<!-- File: /app/views/posts/add.ctp -->
<script type="text/javascript">
$(document).ready(function(){
	$(".chosen").chosen();
});
</script>

<h1>Add Post</h1>
<?php
echo $this->Form->create('Post');
echo $this->Form->input('title');
echo $this->Form->input('body', array('rows' => '3'));
?>
<select class="chosen" name="data[Tag][]" multiple="true" style="width:400px;">
	<?php 
		foreach($tags as $tag):
 			echo '<option value="'.$tag['Tag']['id'].'">'.$tag['Tag']['name'].'</option>';
 		endforeach;
 	?>
</select>
<input type='hidden' name='data[Post][user_id]' value='<?php echo $this->Session->read('Auth.User.id');?>' >
<?php
echo $this->Form->end('Save Post');
?>