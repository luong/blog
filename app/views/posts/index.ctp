<!-- File: /app/views/posts/index.ctp  (edit links added) -->
<?php $this->Html->css('style', null, array('inline' => false));?>

<h1>Blog posts</h1>
<p><?php echo $this->Html->link("Add Post", array('action' => 'add')); ?></p>

<?php $this->Paginator->options(array('url' => $this->passedArgs)); ?>

<?php 
echo $this->Form->create('Post',array('action'=>'search'));
echo $this->Form->input('keyword');
echo $this->Form->submit('Search');
echo $this->Form->end();
?>
	
<table>
	<tr>
		<th><?php echo $this->Paginator->sort('ID', 'id'); ?></th>
		<th><?php echo $this->Paginator->sort('Title', 'title'); ?></th>
	    <th><?php echo $this->Paginator->sort('Action', ''); ?></th>
	    <th><?php echo $this->Paginator->sort('Created', 'created'); ?></th>
	</tr>
	    <?php foreach($posts as $post): ?>
	<tr>
	    <td><?php echo $post['Post']['id']; ?> </td>
	    <td><?php echo $this->Html->link($post['Post']['title'], array('action' => 'view', $post['Post']['id'])); ?> </td>
	    <td>
	        <?php echo $this->Html->link(
	            'Delete',
	            array('action' => 'delete', $post['Post']['id']),
	            null,
	            'Are you sure?'
	        )?>
	        <?php echo $this->Html->link('Edit', array('action' => 'edit', $post['Post']['id']));?>
	    </td>
	    <td><?php echo $post['Post']['created']; ?> </td>
	</tr>
	<?php endforeach; ?>
</table>

<div class="pagination">
	<?php $this->Paginator->options(array('url' => $this->passedArgs));?>
	<?php echo $this->Paginator->numbers(array('class' => 'page')); ?>
	<?php echo $this->Paginator->prev('« Previous', null, null, array('class' => 'disable')); ?>
	<?php echo $this->Paginator->next('Next »', null, null, array('class' => 'disable')); ?>
</div>