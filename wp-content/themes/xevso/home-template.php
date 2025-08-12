<?php
/**
*Template Name: Blank Template for Home page
*/
get_header();
?>
<div class="xevso-black-template">
      <?php
      while ( have_posts() ) : the_post();
            the_content();
      endwhile;
      ?>
</div>
<?php get_footer();