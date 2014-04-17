<?php
/*
  * Template name: Kontakt
  */
$k = load_template_class( 'KontaktFrontend' );
if( $k->has_form() ) {
	$k->enqueue_scripts();
}
?>
<?php get_header(); the_post(); ?>

<article class="main_article contact" role="main" itemprop="mainContentOfPage">
	<h1><?php the_title(); ?></h1>
	<?php the_content(); ?>

	<?php if( $k->has_form() ) : ?>
		<?php echo $k->return_form_markup(); ?>
	<?php endif; ?>
</article>

<?php get_footer(); ?>