<?php
/**
 * Master Addons Template with Header/Footer
 */

use MasterHeaderFooter\Master_Header_Footer;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Get the template ID that was stored in wp_query
global $wp_query;
$template_id = $wp_query->get( 'master_template_id' );

get_header();

// Render Master Addons template content
if ( $template_id && class_exists( 'MasterHeaderFooter\Master_Header_Footer' ) ) {
	$content = Master_Header_Footer::render_elementor_content( $template_id );
	if ( ! empty( $content ) ) {
		echo $content;
	} else {
		// Fallback: If no content, show appropriate default content
		if ( is_search() ) {
			?>
			<div class="master-addons-search-results">
				<h1><?php printf( __( 'Search Results for: %s', 'master-addons' ), get_search_query() ); ?></h1>
				<?php
				if ( have_posts() ) {
					while ( have_posts() ) {
						the_post();
						the_title( '<h2><a href="' . get_permalink() . '">', '</a></h2>' );
						the_excerpt();
					}
				} else {
					echo '<p>' . __( 'No results found.', 'master-addons' ) . '</p>';
				}
				?>
			</div>
			<?php
		} elseif ( is_404() ) {
			?>
			<div class="master-addons-404">
				<h1><?php _e( '404 - Page Not Found', 'master-addons' ); ?></h1>
				<p><?php _e( 'The page you are looking for could not be found.', 'master-addons' ); ?></p>
			</div>
			<?php
		} elseif ( is_category() ) {
			?>
			<div class="master-addons-category">
				<h1><?php single_cat_title(); ?></h1>
				<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
				<?php
				if ( have_posts() ) {
					while ( have_posts() ) {
						the_post();
						the_title( '<h2><a href="' . get_permalink() . '">', '</a></h2>' );
						the_excerpt();
					}
				} else {
					echo '<p>' . __( 'No posts found in this category.', 'master-addons' ) . '</p>';
				}
				?>
			</div>
			<?php
		} elseif ( is_tag() ) {
			?>
			<div class="master-addons-tag">
				<h1><?php single_tag_title(); ?></h1>
				<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
				<?php
				if ( have_posts() ) {
					while ( have_posts() ) {
						the_post();
						the_title( '<h2><a href="' . get_permalink() . '">', '</a></h2>' );
						the_excerpt();
					}
				} else {
					echo '<p>' . __( 'No posts found with this tag.', 'master-addons' ) . '</p>';
				}
				?>
			</div>
			<?php
		} elseif ( is_author() ) {
			?>
			<div class="master-addons-author">
				<h1><?php printf( __( 'Posts by %s', 'master-addons' ), get_the_author() ); ?></h1>
				<?php the_archive_description( '<div class="author-description">', '</div>' ); ?>
				<?php
				if ( have_posts() ) {
					while ( have_posts() ) {
						the_post();
						the_title( '<h2><a href="' . get_permalink() . '">', '</a></h2>' );
						the_excerpt();
					}
				} else {
					echo '<p>' . __( 'No posts found by this author.', 'master-addons' ) . '</p>';
				}
				?>
			</div>
			<?php
		} elseif ( is_date() ) {
			?>
			<div class="master-addons-date">
				<h1><?php the_archive_title(); ?></h1>
				<?php
				if ( have_posts() ) {
					while ( have_posts() ) {
						the_post();
						the_title( '<h2><a href="' . get_permalink() . '">', '</a></h2>' );
						the_excerpt();
					}
				} else {
					echo '<p>' . __( 'No posts found for this date.', 'master-addons' ) . '</p>';
				}
				?>
			</div>
			<?php
		} elseif ( is_archive() ) {
			?>
			<div class="master-addons-archive">
				<h1><?php the_archive_title(); ?></h1>
				<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
				<?php
				if ( have_posts() ) {
					while ( have_posts() ) {
						the_post();
						the_title( '<h2><a href="' . get_permalink() . '">', '</a></h2>' );
						the_excerpt();
					}
				} else {
					echo '<p>' . __( 'No posts found.', 'master-addons' ) . '</p>';
				}
				?>
			</div>
			<?php
		} elseif ( is_singular('page') ) {
			?>
			<div class="master-addons-page">
				<?php
				if ( have_posts() ) {
					while ( have_posts() ) {
						the_post();
						the_title( '<h1>', '</h1>' );
						the_content();
					}
				}
				?>
			</div>
			<?php
		} elseif ( is_singular() ) {
			?>
			<div class="master-addons-single">
				<?php
				if ( have_posts() ) {
					while ( have_posts() ) {
						the_post();
						the_title( '<h1>', '</h1>' );
						the_content();
					}
				}
				?>
			</div>
			<?php
		}
	}
} else {
	// Debug: Show what went wrong
	echo '<!-- Master Addons Debug: Template ID = ' . esc_html( $template_id ) . ', Class exists = ' . ( class_exists( 'MasterHeaderFooter\Master_Header_Footer' ) ? 'yes' : 'no' ) . ' -->';
}

get_footer();
