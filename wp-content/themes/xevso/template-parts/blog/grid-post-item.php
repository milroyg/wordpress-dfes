<?php 
$xevso_post_author = xevso_options('xevso_post_author', true);
$xevso_post_date = xevso_options('xevso_post_date', true);
$xevso_cmnt_number = xevso_options('xevso_cmnt_number', true);
$xevso_show_category = xevso_options('xevso_show_category', true);
?>

<div class="col-lg-4 col-md-6 mb-30 grid-post-item single-post-item masonry-item">
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
