<!DOCTYPE html><html>	<head>		<!-- Meta data -->		<meta charset="utf-8" />		<title><?php echo $title; ?></title>                <meta name="viewport" content="width=device-width, initial-scale=1" />                <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon" />        <link rel="stylesheet" media="screen" href="/css/screen.css" />        <!--[if lt IE 9]>		<link rel="stylesheet" href="/css/ie.css">		<![endif]-->	</head>	<body>		<div id="wrapper" class="other-info">            <div id="oi-machine">                <a id="puncher-button" href="/" title="Accueil">                    <span>&#xF011;</span> Puncher !                </a>            </div>            <div id="oi-content">                            <?php if (validation_errors() != "" || (isset($errors) && count($errors) > 0)) { ?>                    <div class="wrong">                        <?php echo validation_errors(); ?>                        <?php                             foreach ($errors as $key => $value) {                                echo $value;                            }                        ?>                    </div>                <?php } ?>                                <?php echo $content; ?>            </div>                        <ul id="navigation">        	            <!--[if lt IE 10]>				<li class="left" style="float: left; padding: 10px; color: #CCC">					L'auteur de ce site vous déconseille l'utilisation d'internet explorer en version inférieure à 10.				</li>				<![endif]--><!-- whitespace				<?php                     foreach ($navigation_items as $url => $name) {                ?>                    --><li><a href="<?php echo $url; ?>"><?php echo $name; ?></a></li><!-- whitespace                <?php                     }                ?>                -->            </ul>        </div>        <script>		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');				  ga('create', 'UA-47813178-1', 'hadrienmp.fr');		  ga('send', 'pageview');				</script>	</body></html>