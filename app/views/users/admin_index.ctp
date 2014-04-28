<h2>Admin</h2>
<?php
echo $this->Html->link('Log out', array('action' => 'admin_logout'));
echo '<br>';
echo '<br>';
echo '<br>';
echo $this->Html->link('Manage Users', array('action' => 'admin_manageUsers'));
echo '<br>';
echo $this->Html->link('Manage Posts', array('controller' => 'Posts', 'action' => 'admin_managePosts'));
?>