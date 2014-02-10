<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head><title>Bienvenue sur <?php echo $site_name; ?> !</title></head>
<body>
<div style="max-width: 800px; margin: 0; padding: 30px 0;">
<table width="80%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="5%"></td>
<td align="left" width="95%" style="font: 13px/18px Arial, Helvetica, sans-serif;">
<h2 style="font: normal 20px/23px Arial, Helvetica, sans-serif; margin: 0; padding: 0 0 18px; color: black;">Bienvenue sur <?php echo $site_name; ?></h2>
Merci de nous avoir rejoint <?php echo $site_name; ?>. Vous trouverez ci-après les détails de votre inscription. Gardez-les précieusement.
Suivez le lien suivant pour vous connecter :<br />
<br />
<big style="font: 16px/18px Arial, Helvetica, sans-serif;"><b><a href="<?php echo site_url('/auth/login/'); ?>" style="color: #3366cc;">Go to <?php echo $site_name; ?> now!</a></b></big><br />
<br />
Ce lien ne fonctionne pas ? Copiez le lien suivant dans la
					barre d'adresse de votre navigateur :<br />
<nobr><a href="<?php echo site_url('/auth/login/'); ?>" style="color: #3366cc;"><?php echo site_url('/auth/login/'); ?></a></nobr><br />
<br />
<br />
<?php if (strlen($username) > 0) { ?>Votre nom d'utilisateur : <?php echo $username; ?><br /><?php } ?>
Votre adresse mail : <?php echo $email; ?><br />
<br />
<br />
Bonne utilisation !<br />
<?php echo $site_name; ?> Team
</td>
</tr>
</table>
</div>
</body>
</html>