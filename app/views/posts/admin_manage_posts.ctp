<script>
$(function() {
	$('#PostAdminManagePostsForm').submit(function(e) {
		location = '/admin/Posts/managePosts/keyword:' + $(this).find('#PostKeyword').val();
		return false;
	});
});
</script>

<?php
	$this->Paginator->options(array('url' => $this->passedArgs));
?>

<h2>Admin Manage posts</h2>	

<?php 
echo $this->Html->link('Return admin index screen', array('controller' => 'Users', 'action' => 'admin_index'));
echo $this->Form->create('Post');
echo $this->Form->input('keyword');
echo $this->Form->submit('Search');
echo $this->Form->end();
?>
	
<table>
	<tr>
		<th><?php echo $this->Paginator->sort('ID', 'id'); ?></th>
		<th><?php echo $this->Paginator->sort('Title', 'title'); ?></th>
	    <th>Action</th>
	    <th><?php echo $this->Paginator->sort('Created', 'created'); ?></th>
	</tr>
	    <?php foreach($posts as $post): ?>
	<tr>
	    <td><?php echo $post['Post']['id']; ?> </td>
	    <td><?php echo $post['Post']['title']; ?> </td>
	    <td>
	        <?php echo $this->Html->link(
	            'Delete',
	            array('action' => 'admin_delete', $post['Post']['id']),
	            null,
	            'Are you sure?'
	        )?>
	        <?php echo $this->Html->link('Edit', array('action' => 'admin_edit', $post['Post']['id']));?>
	    </td>
	    <td><?php echo $post['Post']['created']; ?> </td>
	</tr>
	<?php endforeach; ?>
</table>

<div class="pagination">
	<?php $this->Paginator->options(array('url' => $this->passedArgs));?>
	<?php echo $this->Paginator->numbers(array('class' => 'page')); ?>
	<?php echo $this->Paginator->prev('<< Previous', null, null, array('class' => 'disable')); ?>
	<?php echo $this->Paginator->next('Next >>', null, null, array('class' => 'disable')); ?>
</div>