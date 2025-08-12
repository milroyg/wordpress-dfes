<?php

if( class_exists( 'CSF' ) ) {
    $xevso_team_meta = get_post_meta( $post->ID, 'xevso_teammeta', true );
}
?>
<div class="project-single-area">
    <div class="container">
        <div class="single-teams">
            <div class="single-team-top">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-6 col-lg-5 col-xl-5">
                        <div class="single-team-img">
                            <?php the_post_thumbnail(); ?>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-7 col-xl-7 d-flex flex-wrap align-content-center">
                        <div class="single-team-info">
                            <div class="singlew-tema-name">
                                <h2><?php the_title(); ?></h2>
                                <?php if(!empty($xevso_team_meta['xevso_team_stitle'])) : ?>
                                <h5><?php echo esc_html($xevso_team_meta['xevso_team_stitle']); ?></h5>
                                <?php endif; ?>
                            </div>
                            <?php if(!empty($xevso_team_meta['xevso_team_sort_dec'])){
                                echo '<div class="sort-dec">
                                <p>'.esc_html($xevso_team_meta['xevso_team_sort_dec']).'</p>
                                </div>';
                            }?>
                            <div class="singl-tema-info-list">
                                <ul>
                                    <?php if(!empty($xevso_team_meta['xevso_team_age'])) : ?>
                                        <li><span><?php esc_html_e('Age :','xevso'); ?></span><?php echo esc_html($xevso_team_meta['xevso_team_age']); ?></li>
                                    <?php endif; ?>
                                    <?php if(!empty($xevso_team_meta['xevso_team_blood'])) : ?>
                                        <li><span><?php esc_html_e('Blood Group :','xevso'); ?></span><?php echo esc_html($xevso_team_meta['xevso_team_blood']); ?></li>
                                    <?php endif; ?>
                                    <li class="null"></li>
                                    <?php if(!empty($xevso_team_meta['xevso_team_work'])) : ?>
                                        <li><span><?php esc_html_e('Work Progress :','xevso'); ?></span><?php echo esc_html($xevso_team_meta['xevso_team_work']); ?></li>
                                    <?php endif; ?>
                                    <?php if(!empty($xevso_team_meta['xevso_team_gmail'])) : ?>
                                        <li><span><?php esc_html_e('E-mail :','xevso'); ?></span><?php echo esc_html($xevso_team_meta['xevso_team_gmail']); ?></li>
                                    <?php endif; ?>
                                    <li class="null"></li>
                                    <?php if(!empty($xevso_team_meta['xevso_team_phone'])) : ?>
                                        <li><span><?php esc_html_e('Phone :','xevso'); ?></span><?php echo esc_html($xevso_team_meta['xevso_team_phone']); ?></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            <?php if($xevso_team_meta['xevso_team_socials'])  : ?>
                            <div class="single-team-social">
                                    <ul>
                                        <?php foreach($xevso_team_meta['xevso_team_socials'] as $xevso_team_social ) : ?>
                                        <li><a target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo esc_attr($xevso_team_social['xevso_team_social_label']) ?>" href="<?php echo esc_url($xevso_team_social['xevso_team_social_url']) ?>"><span class="<?php echo esc_attr($xevso_team_social['xevso_team_social_icon']) ?>"></span></a></li>
                                        <?php endforeach ?>
                                    </ul>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="single-team-content">
                <div class="signle-team-histyory">
                    <h2><?php esc_html_e('My history','xevso'); ?></h2>
                </div>
                <?php the_content(); ?>
            </div>
        </div>
    </div>
</div>
