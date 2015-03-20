<!doctype html>

<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

<head>
	<meta charset="utf-8">

	<?php // Google Chrome Frame for IE ?>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<?php // mobile meta (hooray!) ?>
	<meta name="HandheldFriendly" content="True">
	<meta name="MobileOptimized" content="320">
	<meta name="viewport" content="minimum-scale=1.0, width=device-width, user-scalable=no, initial-scale=1.0"/>

	<?php // icons & favicons (for more: http://www.jonathantneal.com/blog/understand-the-favicon/) ?>
	<link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/library/images/apple-icon-touch.png">
	<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png">
	<!--[if IE]>
		<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">
	<![endif]-->
	<?php // or, set /favicon.ico for IE10 win ?>
	<meta name="msapplication-TileColor" content="#f01d4f">
	<meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/library/images/win8-tile-icon.png">
	<meta name="msapplication-config" content="none"/>

	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

	<?php // wordpress head functions ?>
	<?php wp_head(); ?>
	<?php // end of wordpress head ?>


</head>

<body <?php body_class(); ?>>

	<?php 
	/*
	 * Google Analytics
	 * Ne pas déplacer cette ligne.
	 * Ne rien mettre avant
	 */
	echo get_template_part("content","ga");
	?>

	<div id="container">

		<header class="header" role="banner">

			<div id="inner-header" class="wrap cf">

				<?php if( is_front_page() ) : ?>
					<h1>
						<a class="lienhome" rel="nofollow"><?php bloginfo('name'); ?></a>
					</h1>
					<?php else :?>
					<p id="logo" class="h1">
						<a class="lienhome" rel="nofollow"><?php bloginfo('name'); ?></a>
					</p>
				<?php endif; ?>

				<nav role="navigation">
					<?php nav_principal(); ?>
				</nav>

			</div>

		</header>
