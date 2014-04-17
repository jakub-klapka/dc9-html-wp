<?php global $dc9_news; $n =& $dc9_news; ?>
<?php $u = load_template_class( 'UpdatesFrontend' ); ?>
<?php enqueue_home_styles(); ?>
<?php get_header(); ?>

<article class="main_article home" role="main" itemprop="mainContentOfPage">



	<?php $updates = $u->setup_updates(); ?>
	<?php if( $updates->have_posts() ): ?>

	<h1 class="title"><?php _e('Novinky projektu', 'Theme'); ?></h1>

		<div class="flash_news">
			<?php while( $updates->have_posts() ): $updates->the_post(); $u->setup_postdata( $updates ); ?>
				<div class="flash_news_item">
					<h2><?php $u->the_content(); ?></h2>
					<div class="date"><?php _e('Přidáno'); ?> <?php echo get_the_date(); ?></div>
				</div>
			<?php endwhile; ?>
			<a class="updates_archive" href="<?php $u->the_updates_archive_link(); ?>"><?php _e('Archív aktualizací', 'Theme'); ?></a>
		</div>
		<?php wp_reset_postdata(); ?>
	<?php endif; ?>

	<?php set_home_news_query(); ?>
	<?php if( have_posts() ): while( have_posts() ): the_post(); ?>

		<?php get_template_part( 'template_part_news_item' ); ?>

	<?php endwhile; endif; ?>
	<?php wp_reset_query(); ?>

	<a class="archive" href="<?php $n->the_news_archive_link(); ?>"><?php _e('Archív novinek', 'Theme'); ?></a>

</article>

<?php get_footer(); ?>