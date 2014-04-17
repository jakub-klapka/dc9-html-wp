<?php $gf = load_template_class( 'GalerieFrontend' ); ?>
<?php get_header(); the_post(); ?>

<article class="main_article gallery" role="main" itemprop="mainContentOfPage">
	<h1><?php the_title(); ?></h1>
	<?php the_content(); ?>
	<?php $gf->generate_gallery(); ?>
</article>

<?php get_footer(); ?>