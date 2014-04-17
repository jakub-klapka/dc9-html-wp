<?php global $dc9_layout;
$dc9_layout->enqueue_novinky_single_style();
?>
<?php get_header(); the_post(); ?>

<article class="main_article single-novinky" role="main" itemprop="mainContentOfPage">
	<h1><?php the_title(); ?></h1>
	<?php the_content(); ?>
	<div class="adjacent_links">
		<?php $previous_url = get_url_from_anchor( get_next_post_link() ); ?>
		<?php $next_url = get_url_from_anchor( get_previous_post_link() ); ?>
		<?php if( $previous_url !== false ): ?>
			<a href="<?php echo $previous_url['url']; ?>" class="previous"><?php echo $previous_url['name']; ?></a>
		<?php endif; ?>
		<?php if( $next_url !== false ): ?>
			<a href="<?php echo $next_url['url']; ?>" class="next"><?php echo $next_url['name']; ?></a>
		<?php endif; ?>

	</div>
</article>

<?php get_footer(); ?>