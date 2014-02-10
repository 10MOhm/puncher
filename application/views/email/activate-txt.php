Bienvenue sur <?php echo $site_name; ?>,

Vous trouverez ci-après les détails de votre inscription. Gardez-les précieusement.
Pour confirmer votre adresse mail, cliquez sur le lien ci-dessous :

<?php echo site_url('/auth/activate/'.$user_id.'/'.$new_email_key); ?>


Veuillez confirmer votre email dans les <?php echo $activation_period; ?> heures. Dans le cas contraire, votre inscription sera invalidée et vous devrez vous réinscrire.
<?php if (strlen($username) > 0) { ?>

Votre nom d'utilisateur : <?php echo $username; ?>
<?php } ?>

Votre adresse mail : <?php echo $email; ?>

Bonne utilisation
<?php echo $site_name; ?> Team