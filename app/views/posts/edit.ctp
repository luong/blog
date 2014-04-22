<!-- File: /app/views/posts/edit.ctp -->
<script type="text/javascript">
$(document).ready(function(){
	$(".chosen").chosen();
});
</script>

<h1>Edit Post</h1>
<?php
    echo $this->Form->create('Post', array('action' => 'edit'));
    echo $this->Form->input('title');
    echo $this->Form->input('body', array('rows' => '3'));
    echo $this->Form->input('id', array('type' => 'hidden'));
?>
<select class="chosen" name="data[Tag][]" multiple="true" style="width:400px;">
	<?php foreach($tags as $tag): ?>
 			<option value="<?php echo $tag['Tag']['id'];?>"
 				<?php 
 					foreach ($this->data['Tag'] as $selectedTags){
						if($tag['Tag']['id'] == $selectedTags['id']){
							echo "Selected";
							break;
						}	
					}?>>
 				<?php echo $tag['Tag']['name']; ?>
 			</option>
 	<?php endforeach; ?>
</select>
<?php 
    echo $this->Form->end('Save Post');
?>