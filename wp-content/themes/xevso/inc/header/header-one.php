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
                <a href="#"><button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navmenu">
                        <span class="main-menu-btn">
                            <span class="main-menu-btn-icon"></span>
                        </span>
                    </button></a> 
                    <?php get_template_part('inc/header/header','nav'); ?>
                </div>
				
				<div class="right-part tow">
					<ul class="d-flex align-items-center">
						<li>
							<button data-toggle="modal" data-target="#exampleModal"><i class="flaticon-loupe"></i></button>
						</li>
					</ul>
				</div>
				
				
<!-- start modal area -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">search here</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label=""><i class="ico ico-cross"></i></button>
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
				

            </nav>
        </div>
    </div>
</div>