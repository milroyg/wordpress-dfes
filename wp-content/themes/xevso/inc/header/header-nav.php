<?php
    wp_nav_menu( array(
        'container_id'=>'navmenu',
        'container_class'=>'collapse navbar-collapse',
        'menu_class'=>'navbar-nav sm sm-simple',
        'menu_id'=>'main-menu',
        'theme_location'=>'main-menu',
        'echo'            => true,
        'fallback_cb'     => false,
    ) );
?>