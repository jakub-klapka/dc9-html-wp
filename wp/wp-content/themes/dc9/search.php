<?php enqueue_news_archive_style(); ?>
<?php get_header(); ?>

	<article class="main_article home" role="main" itemprop="mainContentOfPage">
		<h1 class="title"><?php _e( 'Výsledky hledání pro:', 'Theme' ); ?> &quot;<?php the_search_query(); ?>&quot;</h1>

		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'template_part_news_item' ); ?>

		<?php endwhile; else: ?>
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