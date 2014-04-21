<!-- File: /app/views/posts/add.ctp -->
<?php 
echo $this->Html->css(array('style','chosen'), null, array('inline' => false));?>
<?php echo $this->Html->script(array('jquery', 'chosen.jquery'));?>

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
<select class="chosen" name="data[Post][tag][]" multiple="true" style="width:400px;">
	<?php 
		foreach($tags as $tag):
 			echo '<option value="'.$tag['Tag']['id'].'">'.$tag['Tag']['name'].'</option>';
 		endforeach;
 	?>
</select>
<?php
echo $this->Form->end('Save Post');
?>