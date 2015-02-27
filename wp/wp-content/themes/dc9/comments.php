<?php
/**
 * The template for displaying Comments
 *
 * The area of the page that contains comments and the comment form.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>

	<h2 class="comments-title">
		<?php
			printf( _n( 'Jeden komentář k &ldquo;%2$s&rdquo;', '%1$s komentářů k &ldquo;%2$s&rdquo;', get_comments_number(), 'Theme' ),
				number_format_i18n( get_comments_number() ), get_the_title() );
		?>
	</h2>

	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
	<nav id="comment-nav-above" class="navigation comment-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Navigace', 'Theme' ); ?></h1>
		<div class="nav-previous"><?php previous_comments_link( __( '&larr; Starší komentáře', 'Theme' ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( __( 'Novější komentáře &rarr;', 'Theme' ) ); ?></div>
	</nav><!-- #comment-nav-above -->
	<?php endif; // Check for comment navigation. ?>

	<ol class="comment-list">
		<?php
			wp_list_comments( array(
				'style'      => 'ol',
				'short_ping' => true,
				'avatar_size'=> 34,
			) );
		?>
	</ol><!-- .comment-list -->

	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
	<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Navigace', 'Theme' ); ?></h1>
		<div class="nav-previous"><?php previous_comments_link( __( '&larr; Starší komentáře', 'Theme' ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( __( 'Novější komentáře &rarr;', 'Theme' ) ); ?></div>
	</nav><!-- #comment-nav-below -->
	<?php endif; // Check for comment navigation. ?>

	<?php if ( ! comments_open() ) : ?>
	<p class="no-comments"><?php _e( 'Komentáře jsou uzavřeny.', 'Theme' ); ?></p>
	<?php endif; ?>

	<?php endif; // have_comments() ?>

	<?php

	$fields   =  array(
		'author' => '<p class="comment-form-author">' . '<label for="author">' . __( 'Name' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
		            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
		'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email' ) . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
		            '<input id="email" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30" aria-describedby="email-notes"' . $aria_req . ' /></p>',
	);

	comment_form( array(
		'fields' => apply_filters( 'comment_form_default_fields', $fields ),
		'comment_notes_before' => '<p class="comment-notes">' . __( 'Pokud zadáte e-mailovou adresu, dostanete upozornění na nové komentáře. Váš e-mail nebude nikde zobrazen.', 'Theme' ) . '</p>',
		'comment_notes_after' => '',
		'comment_field' => '<p class="comment-form-comment"><label for="comment">' . _x( 'Comment', 'noun' ) . '</label><textarea required id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>'
	) ); ?>

</div><!-- #comments -->
