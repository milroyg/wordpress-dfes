<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package xevso
 */

get_header();
if(get_post_meta( get_the_ID(), 'xevso_metabox', true)) {
      $xevso_commonMeta = get_post_meta( get_the_ID(), 'xevso_metabox', true );
  } else {
      $xevso_commonMeta = array();
  }
  
  if(array_key_exists('xevso_layout_meta', $xevso_commonMeta)){
      $xevso_postLayout = $xevso_commonMeta['xevso_layout_meta'];
  }else{
      $xevso_postLayout = 'full-width';
  }
  
  if(array_key_exists('xevso_sidebar_meta', $xevso_commonMeta)){
      $xevso_selectedSidebar = $xevso_commonMeta['xevso_sidebar_meta'];
  }else{
      $xevso_selectedSidebar = 'sidebar';
  }
  
  if($xevso_postLayout == 'left-sidebar' && is_active_sidebar($xevso_selectedSidebar) || $xevso_postLayout == 'right-sidebar' && is_active_sidebar($xevso_selectedSidebar)){
      $xevso_pageColumnClass = 'col-lg-8';
  }else{
      $xevso_pageColumnClass = 'col-lg-12';
  }
  
  if(array_key_exists('xevso_meta_enable_banner', $xevso_commonMeta)){
      $xevso_postBanner = $xevso_commonMeta['xevso_meta_enable_banner'];
  }else{
      $xevso_postBanner = true;
  }
  if(array_key_exists('xevso_meta_custom_title', $xevso_commonMeta)){
      $xevso_customTitle = $xevso_commonMeta['xevso_meta_custom_title'];
  }else{
      $xevso_customTitle = '';
  }
  ?>
  <?php if($xevso_postBanner == true ) : ?>
      <div class="breadcroumb-boxs">
            <div class="container">
                  <div class="breadcroumb-box">
                        <div class="brea-title">
                              <h2>
                                    <?php 
                                    if(!empty($xevso_customTitle)){
                                          echo esc_html($xevso_customTitle);
                                    }else{
                                          the_title();
                                    }
                                    ?>
                              </h2>
                        </div>
                        <?php if(function_exists('bcn_display')) : ?>
                              <div class="breadcrumb-bcn">
                                    <?php bcn_display();?>
                              </div>
                        <?php endif; ?>
                  </div>
            </div>
      </div>
  <?php endif; ?>
<div class="default-page-section">
      <div class="container">
            <div class="row">
                  <?php
			if($xevso_postLayout == 'left-sidebar' && is_active_sidebar($xevso_selectedSidebar)){
			      get_sidebar();
			}
			?>
                  <div class="team-single-page <?php echo esc_attr($xevso_pageColumnClass);?>">
                        <div class="theme-content layout-<?php echo esc_attr($xevso_postLayout);?>">
                        <?php
                              while ( have_posts() ) :
                                    the_post();
                                    get_template_part( 'template-parts/content', get_post_type() );
                              endwhile; 
                              ?>
                        </div>
                  </div>
                  <?php
			if($xevso_postLayout == 'right-sidebar' && is_active_sidebar($xevso_selectedSidebar)){
				get_sidebar();
			}?>
            </div>
      </div>	
</div>
<?php get_footer(); ?>
