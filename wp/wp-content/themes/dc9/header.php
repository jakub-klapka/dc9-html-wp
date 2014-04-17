<!doctype html>
<html <?php language_attributes(); ?> itemscope itemtype="http://schema.org/WebPage">
<head>
	<meta charset="UTF-8">
	<title itemprop="name"><?php wp_title(); ?></title>

	<script type="text/javascript">window.theme_url = '<?php echo get_template_directory_uri(); ?>/';</script>

	<?php wp_head(); ?>

	<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/images/favicon/favicon.ico">
	<link rel="apple-touch-icon" sizes="57x57" href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="114x114" href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="72x72" href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="144x144" href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="60x60" href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="120x120" href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="76x76" href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="152x152" href="<?php echo get_template_directory_uri(); ?>/images/favicon/apple-touch-icon-152x152.png">
	<link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/images/favicon/favicon-196x196.png" sizes="196x196">
	<link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/images/favicon/favicon-160x160.png" sizes="160x160">
	<link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/images/favicon/favicon-96x96.png" sizes="96x96">
	<link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/images/favicon/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="<?php echo get_template_directory_uri(); ?>/images/favicon/favicon-16x16.png" sizes="16x16">
	<meta name="msapplication-TileColor" content="#da3610">
	<meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/images/favicon/mstile-144x144.png">
	<meta name="msapplication-square70x70logo" content="<?php echo get_template_directory_uri(); ?>/images/favicon/mstile-70x70.png">
	<meta name="msapplication-square144x144logo" content="<?php echo get_template_directory_uri(); ?>/images/favicon/mstile-144x144.png">
	<meta name="msapplication-square150x150logo" content="<?php echo get_template_directory_uri(); ?>/images/favicon/mstile-150x150.png">
	<meta name="msapplication-square310x310logo" content="<?php echo get_template_directory_uri(); ?>/images/favicon/mstile-310x310.png">
	<meta name="msapplication-wide310x150logo" content="<?php echo get_template_directory_uri(); ?>/images/favicon/mstile-310x150.png">

</head>
<body>

<div class="page_wrap">

	<header class="main_header" role="banner">

		<div class="language_switcher" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
			<span class="hidden"><?php _e('Vyberte jazyk', 'Theme'); ?></span>
			<?php dc9_generate_lang_switcher(); ?>
		</div>

		<div class="main_logo_text">
			<a href="<?php bloginfo('url'); ?>"><h1 itemprop="name">Czech<br> DC-9<br> Project</h1></a>
		</div>

		<a href="<?php bloginfo('url'); ?>" class="main_logo_image"><img src="<?php echo get_template_directory_uri(); ?>/images/main_logo.jpg" alt="<?php _e('Obrázek NWA DC9 v letu'); ?>" width="710" height="250" /></a>

		<div class="tagline"><?php dc9_get_the_option( 'description_under_header' ); ?></div>

	</header>

	<div class="content">