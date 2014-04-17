<?php $vf = load_template_class( 'VideoFrontend' ); ?>
<?php $vf->enqueue_video_gallery_styles(); ?>
<?php get_header(); ?>

<article class="main_article video_gallery" role="main" itemprop="mainContentOfPage">

	<h1><?php $vf->generate_parent_anchors( '&nbsp;/&nbsp;' ); ?> <?php _e('Videa', 'Theme'); ?></h1>

	<?php if( $vf->has_children() ): $children = $vf->get_children() ?>

		<div class="subcategories">
			<h2><?php _e('Podkategorie', 'Theme'); ?>:</h2>
			<?php foreach( $children as $child ): ?>
				<a href="<?php echo $child['url']; ?>"><?php echo $child['name']; ?></a>
			<?php endforeach; ?>
		</div>

	<?php endif; ?>


	<?php if( have_posts() ): while( have_posts() ): the_post(); ?>

		<div class="video_item">
			<a class="video_link" href="http://www.youtube.com/watch?v=<?php $vf->the_video_id(); ?>" data-video-id="<?php $vf->the_video_id(); ?>">
				<div class="image">
					<img src="<?php $vf->the_video_thumb_url(); ?>" alt="" width="160" height="90" />
				</div>
				<div class="name_and_desc">
					<time><?php echo get_the_date(); ?></time>
					<h2><?php $vf->the_title(); ?></h2>
					<?php $vf->the_content(); ?>
				</div>
			</a>
		</div>

	<?php endwhile; endif; ?>

	<?php if( $vf->is_paginated() ): ?>
		<div class="pagination">
			<?php if( !$vf->is_first_page() ): ?>
				<a class="newer" href="<?php echo $vf->previous_page_url(); ?>"><?php _e('Předchozí videa', 'Theme'); ?></a>
			<?php else: ?>
				<div class="placeholder" aria-hidden="true"></div>
			<?php endif; ?>
			<div class="pages">
				<?php foreach( $vf->get_pagination_links() as $link ): ?>
					<?php echo $link; ?>
				<?php endforeach; ?>
			</div>
			<?php if( !$vf->is_last_page() ): ?>
				<a class="older" href="<?php echo $vf->next_page_url(); ?>"><?php _e('Další videa', 'Theme'); ?></a>
			<?php else: ?>
				<div class="placeholder" aria-hidden="true"></div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

</article>

<?php get_footer(); ?>