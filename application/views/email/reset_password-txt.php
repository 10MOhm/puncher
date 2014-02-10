Bonjour<?php if (strlen($username) > 0) { ?> <?php echo $username; ?><?php } ?>,

Vous avez changé votre mot de passe.
S'il vous plaît, conservez-le précieusement en tête.
<?php if (strlen($username) > 0) { ?>

Votre nom d'utilisateur : <?php echo $username; ?>
<?php } ?>

Votre adresse mail : <?php echo $email; ?>

<?php /* Your new password: <?php echo $new_password; ?>

*/ ?>

Merci,
<?php echo $site_name; ?> Team