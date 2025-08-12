<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package xevso
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>
<div class="xevso-post-comment-area">
	<div id="comments" class="comments-area">
		<?php
		// You can start editing here -- including this comment!
		if ( have_comments() ) :
			?>
			<h2 class="comments-title">
				<?php
				$xevso_comment_count = get_comments_number();
				if ( '1' === $xevso_comment_count ) {
					printf( _nx( 'No Comment', '%1$s Comments', get_comments_number(), 'comments title', 'xevso' ), number_format_i18n( get_comments_number() ) );
				} else {
					printf( _nx( 'One Comment', '%1$s Comments', get_comments_number(), 'comments title', 'xevso' ), number_format_i18n( get_comments_number() ) );
				}
				?>
			</h2><!-- .comments-title -->
			<div class="comment-area">
				<div class="comment-list">
				<?php
					wp_list_comments( array(
						'avatar_size'=>	80,
						'short_ping' => true,
						'style'      => 'ol',
						'callback'	 =>'xevso_comment'
					) );
				?>
				</div>
			</div>
			<div class="xevso-comment-nav">
				<?php 
				$paginet = array(
					'prev_text' => '<i class="fa fa-angle-left"></i>',
					'next_text' => '<i class="fa fa-angle-right"></i>',
					'screen_reader_text' => ' ',
					'type' => 'array',
					'show_all' => true,
				);
				the_comments_pagination( $paginet );
				?>
			</div>
			<?php
			// If comments are closed and there are comments, let's leave a little note, shall we?
			if ( ! comments_open() ) :
				?>
				<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'xevso' ); ?></p>
				<?php
			endif;
		endif; // Check for have_comments().
		?>
		<div class="xevso-comments">
			<div class="xevso-comment-area">
				<?php
				$xevso_comment_fields = array();
				$xevso_name_field_placeholder = esc_html__('Name','xevso');
				$xevso_email_field_placeholder = esc_html__('Email','xevso');
				$xevso_url_field_placeholder = esc_html__('Website','xevso');
				$xevso_send_field_placeholder = esc_html__('Submit','xevso');
				$xevso_comment_field_placeholder = esc_html__('Comment','xevso');
				$xevso_comment_fields['author']=<<<EOD
	<div class="row">
		<div class="xevso-comment-filed col-md-4">
			<div class="input-group">
				<input type="text" id="author" name="author"  class="form-control" placeholder="{$xevso_name_field_placeholder}*" required >
			</div>
		</div>
	EOD;
	$xevso_comment_fields['email'] = <<<EOD
		<div class="xevso-comment-filed col-md-4">
			<div class="input-group">
				<input type="email" id="email" name="email" class="form-control" placeholder="{$xevso_email_field_placeholder}*" required >
			</div>
		</div>
	EOD;
	$xevso_comment_fields['url'] = <<<EOD
		<div class="xevso-comment-filed col-md-4">
			<div class="input-group">
				<input type="text" id="url" name="url" class="form-control" placeholder="{$xevso_url_field_placeholder}" required >
			</div>
		</div>
	</div>
	EOD;
	$xevso_comment_field = <<<EOD
		<div class="xevso-comment-filed">
			<div class="input-group">
			<textarea id="comment" name="comment" cols="50" rows="5" placeholder="{$xevso_comment_field_placeholder}"class="form-control"></textarea>
			</div>
		</div>
	EOD;
	$xevso_comment_submit_button = <<<EOD
		<div class="xevso-comment-filed">
			<div class="xevso-cm-btns">
				<div class="input-group">
					<button type="submit" class="blob-btn">{$xevso_send_field_placeholder}
						<span class="blob-btn__inner">
						</span>
					</button>
				</div>
			</div>
		</div>
	EOD;

				$xevso_comment_form_arguments = array(
				'fields'=>$xevso_comment_fields,
				'comment_field'=>$xevso_comment_field,
				'submit_button'=>$xevso_comment_submit_button,
				'title_reply'=> esc_html__('Post Comment','xevso')
				);
				comment_form($xevso_comment_form_arguments);
				?>
			</div>
		</div>
	</div>
</div>