<?php
$password = array(
	'name'	=> 'password',
	'id'	=> 'password',
	'size'	=> 30,
);
$email = array(
	'name'	=> 'email',
	'id'	=> 'email',
	'value'	=> set_value('email'),
	'maxlength'	=> 80,
	'size'	=> 30,
);
?>
<?php echo form_open($this->uri->uri_string()); ?>
<div class="form-wrapper">
    <table>
    	<tr>
    		<td><?php echo form_label($this->lang->line('Password'), $password['id']); ?></td>
    		<td class="<?php echo form_error($password['name']) != "" || isset($errors[$password['name']]) ? "wrong" : "" ?>">
                <?php echo form_password($password); ?>
            </td>
    	</tr>
    	<tr>
    		<td><?php echo form_label($this->lang->line('New email address'), $email['id']); ?></td>
    		<td class="<?php echo form_error($email['name']) != "" || isset($errors[$email['name']]) ? "wrong" : "" ?>">
                <?php echo form_input($email); ?>
            </td>
    	</tr>
    </table>
</div>
<div class="buttons-bar">
    <?php echo form_submit('change', $this->lang->line('Send confirmation email')); ?>
</div>
<?php echo form_close(); ?>