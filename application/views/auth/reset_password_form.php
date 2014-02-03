<?php
$new_password = array(
	'name'	=> 'new_password',
	'id'	=> 'new_password',
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size'	=> 30,
);
$confirm_new_password = array(
	'name'	=> 'confirm_new_password',
	'id'	=> 'confirm_new_password',
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size' 	=> 30,
);
?>
<?php echo form_open($this->uri->uri_string()); ?>
<div class="form-wrapper">
<table>
	<tr>
		<td><?php echo form_label($this->lang->line('New Password'), $new_password['id']); ?></td>
		<td class="<?php echo form_error($new_password['name']) != "" || isset($errors[$new_password['name']]) ? "wrong" : "" ?>">
            <?php echo form_password($new_password); ?>
        </td>
	</tr>
	<tr>
		<td><?php echo form_label($this->lang->line('Confirm New Password'), $confirm_new_password['id']); ?></td>
		<td class="<?php echo form_error($confirm_new_password['name']) != "" || isset($errors[$confirm_new_password['name']]) ? "wrong" : "" ?>">
            <?php echo form_password($confirm_new_password); ?>
        </td>
		
	</tr>
</table>
</div>
<div class="buttons-bar">
<?php echo form_submit('change', $this->lang->line('Change Password')); ?>
</div>
<?php echo form_close(); ?>