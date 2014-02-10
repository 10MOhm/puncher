Bonjour <?php if (strlen($username) > 0) { ?> <?php echo $username; ?><?php } ?>,

Vous avez changé votre adresse mail pour <?php echo $site_name; ?>.
Suivez le lien suivant pour confirmer votre adresse :

<?php echo site_url('/auth/reset_email/'.$user_id.'/'.$new_email_key); ?>


Votre nouvelle adresse mail : <?php echo $new_email; ?>


Vous recevez ce mail suite à une requête d'un utilisateur de <?php echo $site_name; ?>. 
Si vous avez reçu ce mail par erreur, NE cliquez PAS sur le lien de confirmation et supprimez ce mail. 
Après une courte période, cette requête sera effacée du système.


Merci,
<?php echo $site_name; ?> Team