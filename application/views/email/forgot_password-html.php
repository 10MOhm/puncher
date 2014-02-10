<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><title>Création d'un nouveau mot de passe pour <?php echo $site_name; ?></title></head>
<body>
<div style="max-width: 800px; margin: 0; padding: 30px 0;">
<table width="80%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="5%"></td>
<td align="left" width="95%" style="font: 13px/18px Arial, Helvetica, sans-serif;">
<h2 style="font: normal 20px/23px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;">
Création d'un nouveau mot de passe pour <?php echo $site_name; ?></h2>
Vous avez oublié votre mot de passe ?<br />
Pour en créer un nouveau, suivez simplement ce lien :<br />
<br />
<big style="font: 16px/18px Arial, Helvetica, sans-serif;"><b><a href="<?php echo site_url('/auth/reset_password/'.$user_id.'/'.$new_pass_key); ?>" style="color: #3366cc;">Créer un nouveau mot de passe</a></b></big><br />
<br />
Ce lien ne fonctionne pas ? Copiez le lien suivant dans la barre d'adresse de votre navigateur :<br />
<nobr><a href="<?php echo site_url('/auth/reset_password/'.$user_id.'/'.$new_pass_key); ?>" style="color: #3366cc;"><?php echo site_url('/auth/reset_password/'.$user_id.'/'.$new_pass_key); ?></a></nobr><br />
<br />
<br />
Vous recevez ce mail suite à une requpête d'un utilisateur de <a href="<?php echo site_url(''); ?>" style="color: #3366cc;"><?php echo $site_name; ?></a>. 
Cette étape fait partie de la procédure pour créer un nouveau mot de passe sur le système.
Si vous N'AVEZ PAS demandé un nouveau mot de passe, NE CLIQUEZ PAS SUR LE LIEN et votre mot de passe ne changera pas.<br />
<br />
<br />
Merci,<br />
<?php echo $site_name; ?> Team
</td>
</tr>
</table>
</div>
</body>
</html>