<?php get_header(); the_post(); ?>

<article class="main_article" role="main" itemprop="mainContentOfPage">
	<h1><?php the_title(); ?></h1>
	<?php the_content(); ?>

	<?php comments_template(); ?>
</article>

<?php get_footer(); ?>