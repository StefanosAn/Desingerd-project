<?php
/**
 * The header.
 *
 * This is the template that displays all of the <head> section and everything up until main.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since Twenty Twenty-One 1.0
 */

?>
<!doctype html>
<html <?php language_attributes(); ?> <?php twentytwentyone_the_html_classes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="alternate"  href="functions.php" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'twentytwentyone' ); ?></a>

	<?php //get_template_part( 'template-parts/header/site-header' ); ?>

	<div id="content" class="site-content">
		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">
			<?php 	
			global $post;
			$post_slug = $post->post_name;
			print_r($post_slug, true);

		?>
		<header>	
			<?php
			if(page_access(true)){ ?>
				<a id="logo" href="https://wordpress.designerd.gr/home/"> <img src="https://wordpress.designerd.gr/wp-content/uploads/2022/02/designerd_logo.png" alt="Designerd"> </a>										

				<nav>
					<?php echo get_menu($post_slug); ?>					
				</nav> 
	<?php	}else{ ?>
				<p id="logoCenter"><img src="https://wordpress.designerd.gr/wp-content/uploads/2022/02/designerd_logo.png" alt="Designerd"></p>								
	<?php	} ?>					
		</header>
