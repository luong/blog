<!-- File: /app/views/posts/index.ctp  (edit links added) -->
<?php $this->Html->css('style', null, array('inline' => false));?>

<script>

	function submitSearch() {
		if (document.getElementById("keyword").value != "") {
			document.getElementById("flag").value = "search";
			document.forms['formIndex'].submit();
		}
	}
	
</script>

<h1>Blog posts</h1>
<p><?php echo $this->Html->link("Add Post", array('action' => 'add')); ?></p>

<form id="formIndex" action="<?php echo $this->webroot;?>posts" method="post" >
	<input id="keyword" type="text" name="data[Post][keyword]">
	<input type="submit" onclick="submitSearch()" value="Search">
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
	        <td><?php echo $post['Post']['Created']; ?> </td>
	    </tr>
	    <?php endforeach; ?>
	</table>
	<div id="container">
		<div class="pagination">
			<?php echo $this->Paginator->numbers(array('class' => 'page')); ?>
			<?php echo $this->Paginator->prev('« Previous', array('class' => 'page active'), null, array('class' => 'disable')); ?>
			<?php echo $this->Paginator->next('Next »', array('class' => 'page active'), null, array('class' => 'disable')); ?>
		</div>
	</div>
</form>