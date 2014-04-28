<script>
$(function() {
	$('#UserAdminManageUsersForm').submit(function(e) {
		location = '/admin/Users/manageUsers/keyword:' + $(this).find('#UserKeyword').val();
		return false;
	});
});
</script>

<?php
	$this->Paginator->options(array('url' => $this->passedArgs));
?>

<h2>Admin Manage USers</h2>	

<?php
echo $this->Html->link('Return admin index screen', array('action' => 'admin_index'));
echo $this->Form->create('User');
echo $this->Form->input('keyword');
echo $this->Form->submit('Search');
echo $this->Form->end();
?>
	
<table>
	<tr>
		<th><?php echo $this->Paginator->sort('ID', 'id'); ?></th>
		<th><?php echo $this->Paginator->sort('Email', 'email'); ?></th>
		<th><?php echo $this->Paginator->sort('First Name', 'firstname'); ?></th>
		<th><?php echo $this->Paginator->sort('Last Name', 'lastname'); ?></th>
	    <th>Action</th>
	    <th><?php echo $this->Paginator->sort('Created', 'created'); ?></th>
	</tr>
	    <?php foreach($users as $user): ?>
	<tr>
	    <td><?php echo $user['User']['id']; ?> </td>
	    <td><?php echo $user['User']['email']; ?> </td>
	    <td><?php echo $user['User']['firstname']; ?> </td>
	    <td><?php echo $user['User']['lastname']; ?> </td>
	    <td>
	    	<?php echo $this->Html->link(
	            'Delete',
	            array('action' => 'admin_delete', $user['User']['id']),
	            null,
	            'Are you sure?'
	        )?>
	        <?php echo $this->Html->link('Edit', array('action' => 'admin_edit', $user['User']['id']));?>
	    </td>
	    <td><?php echo $user['User']['created']; ?> </td>
	</tr>
	<?php endforeach; ?>
</table>

<div class="pagination">
	<?php $this->Paginator->options(array('url' => $this->passedArgs));?>
	<?php echo $this->Paginator->numbers(array('class' => 'page')); ?>
	<?php echo $this->Paginator->prev('<< Previous', null, null, array('class' => 'disable')); ?>
	<?php echo $this->Paginator->next('Next >>', null, null, array('class' => 'disable')); ?>
</div>