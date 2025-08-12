<?php
if(get_post_meta( get_the_ID(), 'xevso_project_meta', true)) {
    $projectmeta = get_post_meta( get_the_ID(), 'xevso_project_meta', true );
} else {
    $projectmeta = array();
}
?>
<div class="project-single-area">
    <div class="container">
	<div class="row"> 
        <div class="col-md-6 project-thum">
            <?php the_post_thumbnail('full', array('class' => 'img-responsive')); ?>
        </div>
        <div class="col-md-6 project-info-box pro-category justify-content-between">
		
			<div class="sub-title">
				<h2>Information</h2>
			</div>
		
            <div class="item">
                <?php if( !empty($projectmeta['xevso_pro_title'])) : ?>
                <div class="pro-list"><span><?php esc_html_e('project Title :','xevso'); ?></span><?php echo esc_html($projectmeta['xevso_pro_title']); ?></div>
                <?php endif; ?>
                <?php if( !empty($projectmeta['xevso_pro_client'])) : ?>
                <div class="pro-list"><span><?php esc_html_e('Client :','xevso'); ?></span><?php echo esc_html($projectmeta['xevso_pro_client']); ?></div>
                <?php endif; ?>
            </div>
            <div class="item">
                <?php if( !empty($projectmeta['xevso_pro_status'])) : ?>
                <div class="pro-list"><span><?php esc_html_e('Status :','xevso'); ?></span><?php echo esc_html($projectmeta['xevso_pro_status']); ?></div>
                <?php endif; ?>
                <div class="pro-list"><span><?php esc_html_e('Category : ','xevso') ?></span>
                    <?php $xevso_project_catagory = get_the_terms( get_the_ID(), 'project_cat' ); 
                    foreach($xevso_project_catagory as $project_cat ) :  ?>
                    <?php echo esc_html($project_cat->name) ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="item">
                <?php if( !empty($projectmeta['xevso_pro_date'])) : ?>
                    <div class="pro-list"><span><?php esc_html_e('Date : ','xevso'); ?></span><?php echo esc_html($projectmeta['xevso_pro_date']); ?></div>
                <?php endif; ?>
                <?php if( !empty($projectmeta['xevso_pro_value'])) : ?>
                    <div class="pro-list"><span><?php esc_html_e('Value : ','xevso'); ?></span><?php echo esc_html($projectmeta['xevso_pro_value']); ?></div>
                <?php endif; ?>
            </div>
        </div>
		</div>
        <div class="project-s-contents row">
            <div class="col-lg-12">
                <div class="pro-s-content">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
