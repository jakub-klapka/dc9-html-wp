<div class="news_item">
	<h2 class="same_as_h1"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

	<div class="date"><?php _e('Zveřejněno'); ?> <?php echo get_the_date(); ?></div>

	<?php if( has_post_thumbnail() ): ?>
		<a class="thumbnail" href="<?php the_permalink(); ?>"><img src="<?php the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>"/></a>
	<?php endif; ?>

	<?php if( has_excerpt() ): ?>
		<?php the_excerpt(); ?>
		<a class="more" href="<?php the_permalink(); ?>"><?php _e('Číst více...', 'Theme'); ?></a>
	<?php else: ?>
		<?php the_content(''); ?>
		<?php if( news_has_more() ): ?>
			<a class="more" href="<?php the_permalink(); ?>"><?php _e('Číst více...', 'Theme'); ?></a>
		<?php endif;; ?>
	<?php endif; ?>
</div>