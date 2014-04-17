<?php $gf = load_template_class( 'GalerieFrontend' ); ?>
<?php $gf->enqueue_archive_style(); ?>
<?php get_header(); ?>

<article class="main_article gallery_archive" role="main" itemprop="mainContentOfPage">

	<h1><?php _e( 'Galerie', 'Theme' ); ?></h1>

	<?php if( have_posts() ): while( have_posts() ): the_post(); ?>

		<a class="gallery" href="<?php the_permalink(); ?>">
			<img src="<?php $gf->the_featured_image(); ?>" alt="<?php the_title(); ?>" width="430" height="200" />
			<div class="description">
				<?php _e( 'Galerie', 'Theme' ); ?> #<?php $gf->the_gallery_number(); ?>: <br/>
				<h2 class="name"><?php the_title(); ?></h2>
				<time class="update"><?php _e('Aktualizováno', 'Theme'); ?> <?php $gf->the_modified_date(); ?></time>
			</div>
		</a>

	<?php endwhile; endif; ?>

</article>

<?php get_footer(); ?>