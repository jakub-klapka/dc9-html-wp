<?php
	global $classes_path; require $classes_path . 'VideoFrontend.php';
	global $dc9_video_frontend; $vf =& $dc9_video_frontend;
?>
<?php get_header(); the_post(); ?>

<article class="main_article" role="main" itemprop="mainContentOfPage">
	<h1><?php $vf->the_title(); ?></h1>
	<?php $vf->the_content(); ?>
	<iframe width="670" height="376.875" src="//www.youtube.com/embed/<?php $vf->the_video_id(); ?>?feature=player_embedded&autoplay=0&fs=1&autohide=1&color=white&theme=light" frameborder="0" allowfullscreen></iframe>
</article>

<?php get_footer(); ?>