		<aside class="main_sidebar" role="complementary">
			<h2 class="hidden"><?php _e('Postranní panel', 'Theme'); ?></h2>
			<form class="search" action="<?php bloginfo( 'url' ); ?>" role="search">
				<h3 class="hidden"><?php _e('Hledat', 'Theme'); ?></h3>
				<label><span class="hidden"><?php _e('Hledat', 'Theme'); ?>:</span><input type="text" name="s" placeholder="<?php _e('Hledat', 'Theme'); ?>"/></label>
				<button type="submit"><?php _e('Odeslat', 'Theme'); ?></button>
			</form>
			<nav class="main_menu" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
				<h3 class="hidden"><?php _e('Menu', 'Theme'); ?></h3>
				<?php dc9_generate_main_menu(); ?>
			</nav>
			<section class="subscription">
				<a class="newsletter" href="<?php echo get_permalink( 4581 ); ?>">Newsletter</a>
				<a class="rss" href="<?php echo trailingslashit( get_bloginfo('url') ); ?>feed/"><?php _e('RSS Feed', 'Theme'); ?></a>
			</section>
			<?php $friends_links = dc9_get_option( 'friends_links' ); ?>
			<?php if( !empty( $friends_links ) ): ?>
				<section class="friends_links">
					<?php foreach( $friends_links as $link ): ?>
						<a href="<?php echo esc_url( $link['url'] ); ?>"><?php echo $link['title']; ?></a>
					<?php endforeach; ?>
				</section>
			<?php endif; ?>
		</aside>

		<a class="copyright" href="http://www.lumiart.cz" target="_blank">
			<img src="<?php echo get_template_directory_uri(); ?>/images/copyright.png" alt="Design and code by Lumiart" width="351" height="30" />
		</a>

	</div>

</div>

<?php wp_footer(); ?>

</body>
</html>