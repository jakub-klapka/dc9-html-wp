<?php $u = load_template_class( 'UpdatesFrontend' ); $u->enqueue_archive_style(); ?>
<?php get_header(); ?>

<article class="main_article archive_updates" role="main" itemprop="mainContentOfPage">
	<h1><?php _e('Archív aktualizací', 'Theme'); ?></h1>

	<?php if( have_posts() ): ?>
		<?php while( have_posts() ): the_post(); global $post; ?>

		<div class="update_item">
			<time datetime="<?php date_format( date_create( $post->post_date ), 'Y-m-d' ); ?>"><?php _e('Zveřejněno'); ?> <?php echo get_the_date(); ?></time>
			<?php the_content(); ?>
		</div>

		<?php endwhile; ?>
	<?php else: ?>
		<p><?php _e('Omlouváme se, nemáme zde žádné novinky.', 'Theme'); ?></p>
	<?php endif; ?>

	<?php if( is_paginated() ): ?>
		<div class="pagination">
			<?php if( !is_first_page() ): ?>
				<a class="newer" href="<?php the_previous_page_url(); ?>"><?php _e('Předchozí novinky', 'Theme'); ?></a>
			<?php else: ?>
				<div class="placeholder" aria-hidden="true"></div>
			<?php endif; ?>
			<div class="pages">
				<?php foreach( get_news_pagination_links() as $link ): ?>
					<?php echo $link; ?>
				<?php endforeach; ?>
			</div>
			<?php if( !is_last_page() ): ?>
				<a class="older" href="<?php the_next_page_url(); ?>"><?php _e('Další novinky', 'Theme'); ?></a>
			<?php else: ?>
				<div class="placeholder" aria-hidden="true"></div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

</article>

<?php get_footer(); ?>