<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package xevso 
 */
?>
</div><!-- #content -->

    <?php 
    if( class_exists( 'CSF' ) ) {
      get_template_part('inc/footer/footer');
    }else{
      get_template_part('inc/footer/footer-default');
    }
    ?>
</div><!-- #page -->
<div class="btn-svg">
<svg xmlns="http://www.w3.org/2000/svg" version="1.1">
  <defs>
    <filter id="goo">
      <feGaussianBlur in="SourceGraphic" result="blur" stdDeviation="10"></feGaussianBlur>
      <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0 0 1 0 0 0 0 0 1 0 0 0 0 0 21 -7" result="goo"></feColorMatrix>
      <feBlend in2="goo" in="SourceGraphic" result="mix"></feBlend>
    </filter>
  </defs>
</svg>
</div>
<div class="to-top" id="back-top"><i class="fa fa-chevron-up"></i></div>
<?php wp_footer(); ?>

<script>
jQuery(document).ready(function($) {
    $(document).on('click', 'a', function(e) {
        var link = $(this);
        if (link.length && link[0].hostname !== window.location.hostname) {
            var confirmLeave = confirm('You are about to leave this site. Continue?');
            if (!confirmLeave) {
                e.preventDefault();
            }
        }
    });
});
</script>






</body>

</html>

