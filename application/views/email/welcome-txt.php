Bienvenue sur <?php echo $site_name; ?>,

Merci de nous avoir rejoint <?php echo $site_name; ?>. Vous trouverez ci-après les détails de votre inscription. Gardez-les précieusement.
Suivez le lien suivant pour vous connecter :

<?php echo site_url('/auth/login/'); ?>

<?php if (strlen($username) > 0) { ?>

Votre nom d'utilisateur : <?php echo $username; ?>
<?php } ?>

Votre adresse mail : <?php echo $email; ?>

Bonne utilisation !
<?php echo $site_name; ?> Team