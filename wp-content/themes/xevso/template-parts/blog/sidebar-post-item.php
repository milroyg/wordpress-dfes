<?php 
if(is_archive()){
	$xevso_postItemLayout = xevso_options('xevso_archive_layout', 'right-sidebar');
}else if(is_search()){
	$xevso_postItemLayout = xevso_options('xevso_search_layout', 'right-sidebar');
}else{
	$xevso_postItemLayout = xevso_options('xevso_blog_layout', 'right-sidebar');
}

if($xevso_postItemLayout == 'grid-ls' || $xevso_postItemLayout == 'grid-rs'){
    $xevso_postColumn = 'col-lg-6';
}else{
    $xevso_postColumn = 'col-lg-12';
}

$xevso_post_author = xevso_options('xevso_post_author', true);
$xevso_post_date = xevso_options('xevso_post_date', true);
$xevso_cmnt_number = xevso_options('xevso_cmnt_number', true);
$xevso_show_category = xevso_options('xevso_show_category', true);
?>

<div class="<?php echo esc_attr($xevso_postColumn); ?> col-md-6 mb-30 grid-post-item single-post-item">
    <div id="post-<?php the_ID(); ?>" <?php post_class('post-single'); ?>>
        <?php if(xevso_post_thumbnail()) : ?>
            <div class="img-post">
                <?php xevso_post_thumbnail(); ?>
            </div>
        <?php endif; ?>
        <div class="blog-article">
            <div class="xevso-blog-top">
                <div class="xevso-post-meta">
                    <ul>
                        <?php if($xevso_postItemLayout == 'right-sidebar' || $xevso_postItemLayout == 'left-sidebar') : ?>
                        <li class="post-date"><i class="fa fa-calendar"></i><?php xevso_posted_on(); ?></li>
                        <?php endif; ?>
                        <li class="post-by"><i class="fa fa-user"></i><?php xevso_posted_by(); ?></li>
                        <?php if ( is_singular() ) : ?>
                        <li class="xevso-cat"><i class="fa fa-repeat"></i><?php xevso_post_cat(); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="xevso-post-title">
                    <h2 class="post-title"><a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
                </div>
                <div class="xevso-cat"><span><?php esc_html_e('In :','xevso') ?></span><?php xevso_post_cat(); ?></div>
            </div>
            <div class="post-summery">
                <p><?php echo wp_trim_words( get_the_content(), 20 ); ?></p>
                <div class="blog-btns">
                    <a class="blob-btn" href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e('Read More','xevso') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
