<h2>Edit USer</h2>
<?php
    echo $this->Form->create('User', array('action' => 'admin_edit'));
    echo $this->Form->input('email');
    echo $this->Form->input('firstname');
    echo $this->Form->input('lastname');
    echo $this->Form->input('id', array('type' => 'hidden'));
    echo $this->Form->end('Update');
?>