<?php
if ($use_username) {
	$username = array(
		'name'	=> 'username',
		'id'	=> 'username',
		'value' => set_value('username'),
		'maxlength'	=> $this->config->item('username_max_length', 'tank_auth'),
		'size'	=> 30,
	);
}
$email = array(
	'name'	=> 'email',
	'id'	=> 'email',
	'value'	=> set_value('email'),
	'maxlength'	=> 80,
	'size'	=> 30,
);
$password = array(
	'name'	=> 'password',
	'id'	=> 'password',
	'value' => set_value('password'),
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size'	=> 30,
);
$confirm_password = array(
	'name'	=> 'confirm_password',
	'id'	=> 'confirm_password',
	'value' => set_value('confirm_password'),
	'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
	'size'	=> 30,
);
$captcha = array(
	'name'	=> 'captcha',
	'id'	=> 'captcha',
	'maxlength'	=> 8,
);
?>

<p class="message">
    Avant de vous inscrire, jetez un oeil à <a href="/info" class="no-button" title="Informations sur le site">notre politique de protection de l'anonymat et de vos données.</a>
</p>

<?php echo form_open($this->uri->uri_string()); ?>
<div class="form-wrapper">
<table>
	<?php if ($use_username) { ?>
	<tr>
		<td><?php echo form_label($this->lang->line('Username'), $username['id']); ?></td>
		<td class="<?php echo form_error($username['name']) != "" || isset($errors[$username['name']]) ? "wrong" : "" ?>">
            <?php echo form_input($username); ?>
        </td>
	</tr>
	<?php } ?>
    <tr>
		<td><?php echo form_label($this->lang->line('Email'), $email['id']); ?></td>
		<td class="<?php echo form_error($email['name']) != "" || isset($errors[$email['name']]) ? "wrong" : "" ?>">
            <?php echo form_input($email); ?>
        </td>
	</tr>
	<tr>
		<td><?php echo form_label($this->lang->line('Password'), $password['id']); ?></td>
		<td class="<?php echo form_error($confirm_password['name']) != "" || isset($errors[$confirm_password['name']]) ? "wrong" : "" ?>">
            <?php echo form_password($password); ?>
        </td>
	</tr>
	<tr>
		<td><?php echo form_label($this->lang->line('Confirm Password'), $confirm_password['id']); ?></td>
		<td class="<?php echo form_error($confirm_password['name']) != "" || isset($errors[$confirm_password['name']]) ? "wrong" : "" ?>">
            <?php echo form_password($confirm_password); ?>
        </td>
	</tr>

	<?php if ($captcha_registration) {
		if ($use_recaptcha) { ?>
	<tr>
		<td colspan="2">
			<div id="recaptcha_image"></div>
            <div id="recaptcha-tools">
                <a href="javascript:Recaptcha.reload()" title="Get an other CAPTCHA">
                    <img src="/images/icons/exchange32.png" alt="Get an other CAPTCHA" />
                </a>
                <div class="recaptcha_only_if_image">
                    <a href="javascript:Recaptcha.switch_type('audio')" title="Get an audio CAPTCHA">
                        <img src="/images/icons/speaker32.png" alt="Get an audio CAPTCHA" />
                    </a>
                </div>
                <div class="recaptcha_only_if_audio">
                    <a href="javascript:Recaptcha.switch_type('image')" title="Get an image CAPTCHA" >
                        <img src="/images/icons/linedpaper32.png" alt="Get an image CAPTCHA" />
                    </a>
                </div>
            </div>
		</td>
	</tr>
	<tr>
		<td>
			<div class="recaptcha_only_if_image">Enter the words above</div>
			<div class="recaptcha_only_if_audio">Enter the numbers you hear</div>
		</td>
		<td>
            <input type="text" id="recaptcha_response_field" name="recaptcha_response_field" />
        </td>
		<?php echo $recaptcha_html; ?>
	</tr>
	<?php } else { ?>
	<tr>
		<td colspan="3">
			<p>Enter the code exactly as it appears:</p>
			<?php echo $captcha_html; ?>
		</td>
	</tr>
	<tr>
		<td><?php echo form_label($this->lang->line('Confirmation Code'), $captcha['id']); ?></td>
		<td class="<?php echo form_error($captcha['name']) != "" || isset($errors[$captcha['name']]) ? "wrong" : "" ?>">
            <?php echo form_input($captcha); ?>
        </td>
	</tr>
	<?php }
	} ?>
</table>
</div>
<div class="buttons-bar">
	<?php echo form_submit('register', $this->lang->line('Register')); ?>
</div>
<?php echo form_close(); ?>