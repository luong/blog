<h2>Admin Login</h2>
<?php
echo $this->Form->create('User');
echo $this->Form->input('Admin.username');
echo $this->Form->input('Admin.password');
echo $this->Form->end('Login');
?>