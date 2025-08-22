<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package xevso
 */
$xevso_single_post_author = xevso_options('xevso_single_post_author', true);
$xevso_single_post_date = xevso_options('xevso_single_post_date', true);
$xevso_single_post_cmnt = xevso_options('xevso_single_post_cmnt', true);
$xevso_single_post_cat = xevso_options('xevso_single_post_cat', true);
$xevso_single_post_tag = xevso_options('xevso_single_post_tag', true);
$xevso_post_share = xevso_options('xevso_post_share', true);
$xevso_author_profile = xevso_options('xevso_author_profile', false);

$code = 'iframe';
if(get_post_meta( get_the_ID(), 'xevso_postmeta_audio', true)) {
	$postaudio = get_post_meta( get_the_ID(), 'xevso_postmeta_audio', true );
} else {
  $postaudio = array();
}

if(get_post_meta( get_the_ID(), 'xevso_postmeta_video', true)) {
	$postvideo = get_post_meta( get_the_ID(), 'xevso_postmeta_video', true );
} else {
  $postvideo = array();
}


if(get_post_meta( get_the_ID(), 'xevso_postmeta_gallery', true)) {
	$postgallery = get_post_meta( get_the_ID(), 'xevso_postmeta_gallery', true );
	$postgallerys = $postgallery['xevso_post_gallery']; // for eg. 15,50,70,125
$gallery_ids = explode( ',', $postgallerys );
} else {
  $postgallery = array();
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php if(!empty($postvideo['xevso_post_video']) && has_post_format( 'video' , get_the_ID() )){
            ?>
            <div class="vendor blog-dec-top">
                <<?php echo esc_attr($code); ?> width="100%" height="100%" src="<?php echo esc_url($postvideo['xevso_post_video']); ?>" frameborder="0" allowfullscreen="false"></<?php echo esc_attr($code); ?>>
            </div>
            <?php 
        }elseif(!empty($postaudio['xevso_post_audio']) && has_post_format( 'audio' , get_the_ID() ) ){
			?>
            <div class="post-audio blog-dec-top">
            <<?php echo esc_attr($code); ?> width="100%" height="100%" scrolling="no" frameborder="no" allow="autoplay" src="<?php echo esc_url($postaudio['xevso_post_audio']) ?>"></<?php echo esc_attr($code); ?>>
            </div>
            <?php 
		}elseif(!empty($gallery_ids) && has_post_format( 'gallery' , get_the_ID() )){
			?>
            <div class="post-gallery blog-dec-top">
                
                <?php 
                    foreach( $gallery_ids as $gallery_id ){
                        echo wp_get_attachment_image( $gallery_id, 'full' );
                    }
                ?>
            </div>
            <?php 
		}else{
			?>
			<div class="blog-dec-img blog-dec-top">
<!-- 				<?php/* xevso_post_thumbnail(); */ ?> -->
			</div>
			<?php 
		}?>
		<div class="blog-article">
			<div class="xevso-blog-top">
				<?php
				if ( 'post' === get_post_type() ) :
					?>
					<div class="xevso-post-meta">
						<ul>
							<?php if ( is_singular() ) : ?>
							<li class="xevso-cat"><?php /* xevso_post_cat(); */ ?></li>
							<?php endif; ?>
						</ul>
					</div>
				<?php endif; 
				?>
			</div>
			<div class="post-summery">
				
<!-- 				<h2><?php/* the_title();*/ ?></h2> -->
				<?php the_content(); ?>
			</div>
			<?php /* if( has_tag() or function_exists( 'xevso_post_share_social' ) ) : ?>
			<div class="xevso-blog-rag-meta row bshadow">
				<?php  if(has_tag()) : ?>
				<div class="entry-f-left">
					<?php xevso_post_tag(); ?>
				</div>
				<?php endif; ?>
				<?php if( function_exists('xevso_post_share_social')) : ?>
				<div class="entry-f-right">
					<div class="xevso-social-share"> 
						<label><?php esc_html_e('Share :','xevso'); ?></label><?php xevso_post_share_social(); ?>
					</div>
				</div>
				<?php endif; ?>
				<li class="post-comment"><i class="flaticon-chat"></i> <?php comments_popup_link('0 Comment', '1 Comment', '% '.esc_html__('Comments','xevso').''); ?></li>		
		
			</div>
			<?php endif; */?>
		</div>
</article>
