<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package xevso
 */

?>

<div id="post-<?php the_ID(); ?>" <?php post_class('post-single'); ?>>
      <?php if( xevso_post_thumbnail() ) : ?>
	<div class="xevso-bimg">
		<?php xevso_post_thumbnail(); ?>		
	</div>
	<?php endif; ?>
	<div class="blog-article">
		<div class="xevso-page-content">
			<?php
			the_content();
			wp_link_pages( array(
				'before'     => '<div class="page-links post-pagination"><p>' . esc_html__( 'Pages:', 'xevso' ).'</p><ul class="page-numbers"><li>',
	            'separator'        => '</li><li>',
	            'after'            => '</li></ul></div>',
	            'next_or_number'   => 'number',
	            'nextpagelink'     => esc_html__( 'Next Page', 'xevso'),
	            'previouspagelink' => esc_html__( 'Prev Page', 'xevso' ),
			) );

			// Show embedded PDF AFTER content if checkbox is checked
    $pdf_file = get_field('upload_pdf');
    $show_pdf = get_field('show_pdf');

    if ($pdf_file && in_array('yes', (array) $show_pdf)) {
        echo '<div class="embedded-pdf">';
        echo do_shortcode('[embeddoc url="' . esc_url($pdf_file['url']) . '"]');
        echo '</div>';
    }
			?>
		</div>
	</div>
</div>
