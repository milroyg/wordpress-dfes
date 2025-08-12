
<div class="row">
      <div class="<?php if(is_active_sidebar('sidebar-1')) : ?>col-lg-8 <?php else : ?>col-lg-11 <?php endif; ?> col-md-12 col-sm-12 col-12">
            <div class="blog-list">
            <?php
                  if ( have_posts() ) :
                        if ( is_home() && ! is_front_page() ) :
                              ?>
                              <header>
                                    <h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
                              </header>
                              <?php
                        endif;
                        while ( have_posts() ) :
                              the_post();
                              get_template_part( 'template-parts/content', get_post_format() );
                        endwhile;
                        xevso_pagination();
                  else :
                        get_template_part( 'template-parts/content', 'none' );
                  endif;
                  ?>
            </div>
      </div>
      <?php get_sidebar(); ?>
</div>
