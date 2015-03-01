<?php get_header(); the_post(); global $dc9_newsletter_data; ?>

<article class="main_article" role="main" itemprop="mainContentOfPage">
	<h1><?php _e( 'Přihlášení k newsletteru', 'Theme' ); ?></h1>

	<p><?php _e( 'Zde můžete zadat svůj e-mail a my vás budeme pravidelně informovat o tom, co je na DC9 nového.', 'Theme' ); ?></p>

	<?php if( isset( $dc9_newsletter_data[ 'error' ] ) ): ?>
		<p style="color: red; font-weight: bold;"><?php echo $dc9_newsletter_data[ 'error' ]; ?></p>
	<?php endif; ?>

	<?php if( isset( $dc9_newsletter_data[ 'success' ] ) ): ?>
		<p style="color: green; font-weight: bold;"><?php echo $dc9_newsletter_data[ 'success' ]; ?></p>
	<?php endif; ?>


	<form action="<?php bloginfo( 'url' ); ?>newsletter" method="post">
		<?php wp_nonce_field( 'newsletter' ); ?>
		<input type="email" required placeholder="you@domain.com" name="email"/>
		<input type="submit" name="newsletter_submit" value="Odeslat"/>
	</form>

</article>

<?php get_footer(); ?>