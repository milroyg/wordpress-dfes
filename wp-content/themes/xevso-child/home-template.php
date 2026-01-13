<?php
/**
 *Template Name: Blank Template for Home page
 */
get_header();
?>
<main class="xevso-black-template" id="content">
      <?php
      while (have_posts()):
            the_post();
            the_content();
      endwhile;
      ?>
</main>
<?php get_footer();