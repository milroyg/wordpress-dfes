<div class="header-two header-section">
    <div class="header2-bottom">
        <div class="container">
            <nav class="navbar navbar-expand-lg">
                <div class="site-branding site-logo-area">
                    <div class="site-logo">
                        <?php
                        if(has_custom_logo()){
                            the_custom_logo();
                        }else{
                            $siteLogo = xevso_options('xevso_header2_logo');
                            if(!empty($siteLogo['url'])){
                                $logoUrl = $siteLogo['url'];
                                ?>
                                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                                    <img src="<?php echo esc_url($logoUrl); ?>" alt="<?php the_title_attribute(bloginfo( 'name' )); ?>" title="<?php the_title_attribute(bloginfo( 'name' )); ?>">
                                </a>
                                <?php 
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="header2-menu-area ml-auto">
                    <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navmenu">
                        <span class="main-menu-btn">
                            <span class="main-menu-btn-icon"></span>
                        </span>
                    </button>
                    <?php get_template_part('inc/header/header','nav'); ?>
                </div>
				
				<div class="right-part">
					<ul class="d-flex align-items-center">
						<li>
							<button data-toggle="modal" data-target="#exampleModal"><i class="fa fa-search"></i></button>
						</li>
					</ul>
				</div>
				
				
<!-- start modal area -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">search here</h5>
                    <button type="button" class="X" data-bs-dismiss="modal" aria-label="X"><i class="fa fa-close" aria-hidden="true"></i></button>
                </div>
                <div class="modal-body">
                    <div class="search-area">
                          <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			                  	<input type="search" class="form-control" placeholder="<?php esc_attr_e( 'Search ', 'bccgs' ) ?>" value="<?php echo esc_attr(get_search_query()) ?>" name="s" title="<?php esc_attr_e( 'Search for:', 'bccgs' ) ?>" />
			                      <button type="submit" class="search-submit"><span class="input-group-addon"><i class="flaticon-loupe" aria-hidden="true"></i></span></button>

			                  </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end modal area -->
				
                <?php
                $header2_quete = xevso_options('xevso_enable_quete');
                $header2_quete_text = xevso_options('intech_header2_quete_text');
                $intech_header2_qselect = xevso_options('intech_header2_quete_link_select');
                $intech_header2_quete_page = xevso_options('intech_header2_quete_page');
                $intech_header2_quete_link = xevso_options('intech_header2_quete_link');
                if(!empty($intech_header2_qselect == '1' )){
                    $xevso_qute_link = get_page_link($intech_header2_quete_page);
                }else{
                    $xevso_qute_link = $intech_header2_quete_link;
                }
                ?>
                <?php if(!empty($header2_quete)) : ?>
                <div class="cta-butons">
                    <div class="theme-btn">
					
                        <a class="blob-btn" href="<?php echo esc_url($intech_header2_quete_page); ?>"><?php echo esc_html($header2_quete_text); ?>
                            <span class="blob-btn__inner">
                                <span class="blob-btn__blobs">
                                
                                </span>
                            </span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</div>