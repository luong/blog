<h2>Register User</h2>
<?php
echo $this->Html->link('Log in', array('action' => 'login'));

echo $this->Form->create('User');
echo $this->Form->input('username');
echo $this->Form->input('password');
echo $this->Form->input('password_confirm', array('type' => 'password'));
echo $this->Form->input('email');
echo $this->Form->input('firstname');
echo $this->Form->input('lastname');
echo $this->Form->end('Register');

?>