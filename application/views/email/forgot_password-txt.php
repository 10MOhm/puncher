Bonjour <?php if (strlen($username) > 0) { ?> <?php echo $username; ?><?php } ?>,

Vous avez oublié votre mot de passe ?
Pour en créer un nouveau, suivez simplement ce lien :

<?php echo site_url('/auth/reset_password/'.$user_id.'/'.$new_pass_key); ?>

Vous recevez ce mail suite à une requpête d'un utilisateur de <?php echo $site_name; ?>. 
Cette étape fait partie de la procédure pour créer un nouveau mot de passe sur le système.
Si vous N'AVEZ PAS demandé un nouveau mot de passe, NE CLIQUEZ PAS SUR LE LIEN et votre mot de passe ne changera pas.


Merci,
<?php echo $site_name; ?> Team