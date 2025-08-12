<?php
function xevso_comment($comment, $args, $depth) {
    if ( 'div' === $args['style'] ) {
        $xevso_tag       = 'div';
        $add_below = 'comment';
    } else {
        $xevso_tag       = 'li';
        $add_below = 'div-comment';
    }?>
    <<?php echo esc_html($xevso_tag); ?> <?php comment_class( empty( $args['has_children'] ) ? 'single-comment' : 'parent single-comment' ); ?> id="comment-<?php comment_ID() ?>"><?php 
    if ( 'div' != $args['style'] ) { ?>
        <div id="div-comment-<?php comment_ID() ?>" class="comment-body"><?php
    } ?>
	        <div class="comment-headers">
		        <div class="comment-img">
		            <?php 
		                if ( $args['avatar_size'] != 0 ) {
		                   echo get_avatar( $comment, $args['avatar_size'] ); 
		                } 
		            ?>
		        </div>
		        <div class="comment-header">
		            <h3 class="comment-title"><?php echo esc_html( get_comment_author() ); ?>
		                <label>
		                    <span class="fa fa-clock-o"></span><?php echo esc_html( get_comment_time() ); ?>
		                </label>
		            </h3>
		        </div>
	        </div>
		     <div class="comment-content">
		         <?php if ($comment->comment_approved == '0') : ?>
		            <em><?php esc_html_e('Your comment is awaiting moderation.','xevso') ?></em>
		        <?php endif; ?>
		        <?php comment_text() ?>
		        <div class="blog-details-reply-box">
		            <div class="comment-reply">
		                <a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ) ?>">
		                    <span class="fa fa-calendar"></span><?php echo esc_html(get_comment_date()); ?>
		                </a>
		                <?php comment_reply_link( 
		            array_merge( 
		                $args, 
		                array( 
		                    'add_below' => $add_below, 
		                    'depth'     => $depth, 
		                    'max_depth' => $args['max_depth'] 
		                ) 
		            ) 
		        );  ?>
		                <?php edit_comment_link(esc_html__('(Edit)','xevso'),'  ','') ?>
		            </div>
		        </div>
		    </div>
        <?php 
    if ( 'div' != $args['style'] ) : ?>
        </div><?php 
    endif;
}