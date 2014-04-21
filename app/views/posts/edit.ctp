<!-- File: /app/views/posts/edit.ctp -->
<?php 
echo $this->Html->css(array('style','chosen'), null, array('inline' => false));?>
<?php echo $this->Html->script(array('jquery', 'chosen.jquery'));?>

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
<select class="chosen" name="data[Post][tag][]" multiple="true" style="width:400px;">
	<?php $selected = explode(',', $selectedTags['PostTag']['tag_id']);?>
	<?php foreach($tags as $tag): ?>
 			<option value="<?php echo $tag['Tag']['id'];?>"
 				<?php 
 					for($i=0;$i<count($selected);$i++){
						if($tag['Tag']['id'] == $selected[$i]){
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