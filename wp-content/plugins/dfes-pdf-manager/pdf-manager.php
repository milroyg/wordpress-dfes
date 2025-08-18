<?php
/**
 * Plugin Name: PDF Manager
 * Description: A plugin to manage pdf and auction PDFs with multilingual titles and shortcode display.
 * Version: 1.2
 * Author: Milroy
 */

if (!defined('ABSPATH')) exit;
$GLOBALS['tam_pdf_shortcode_used'] = false;


// Register Custom Post Type & Taxonomy
function tam_register_post_type() {
    register_post_type('tam_pdf', array(
        'labels'      => array(
            'name'          => __('PDF Manager', 'tam'),
            'singular_name' => __('PDF Manager', 'tam'),
        ),
        'public'      => true,
        'publicly_queryable'  => true,
        'show_in_rest' => true, // ← Add this line
        'has_archive' => true,
        'supports'    => array('title', 'custom-fields'),
        'menu_icon'   => 'dashicons-media-document',
        'taxonomies'  => array('tam_category'),
    ));

    register_taxonomy('tam_category', 'tam_pdf', array(
        'label'        => __('Categories', 'tam'),
        'rewrite'      => array('slug' => 'tam-category'),
        'hierarchical' => true,
    ));
}
add_action('init', 'tam_register_post_type');




// Add a new column to the admin list for PDF Manager
function tam_add_category_column($columns) {
    $columns['tam_category'] = __('Category', 'tam');
    return $columns;
}
add_filter('manage_tam_pdf_posts_columns', 'tam_add_category_column');

// Populate the category column with category names
function tam_populate_category_column($column, $post_id) {
    if ($column === 'tam_category') {
        $terms = get_the_terms($post_id, 'tam_category');

        if (!empty($terms) && !is_wp_error($terms)) {
            $categories = [];

            foreach ($terms as $term) {
                $categories[] = $term->name;
            }

            echo esc_html(implode(', ', $categories));
        } else {
            echo __('No Category', 'tam');
        }
    }
}
add_action('manage_tam_pdf_posts_custom_column', 'tam_populate_category_column', 10, 2);

// Make the category column sortable (optional)
function tam_make_category_column_sortable($columns) {
    $columns['tam_category'] = 'tam_category';
    return $columns;
}
add_filter('manage_edit-tam_pdf_sortable_columns', 'tam_make_category_column_sortable');

// Sort by category (optional)
function tam_sort_by_category($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->get('orderby') === 'tam_category') {
        $query->set('meta_key', 'tam_category');
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'tam_sort_by_category');


// Save Meta Data
function tam_save_meta($post_id) {
    if (isset($_POST['tam_pdf_link'])) update_post_meta($post_id, 'tam_pdf_link', esc_url($_POST['tam_pdf_link']));
    if (isset($_POST['tam_title_en'])) update_post_meta($post_id, 'tam_title_en', sanitize_text_field($_POST['tam_title_en']));
    if (isset($_POST['tam_title_mr'])) update_post_meta($post_id, 'tam_title_mr', sanitize_text_field($_POST['tam_title_mr']));
    if (isset($_POST['tam_title_kok'])) update_post_meta($post_id, 'tam_title_kok', sanitize_text_field($_POST['tam_title_kok']));
}
add_action('save_post', 'tam_save_meta');
// Enqueue Admin Scripts
function tam_enqueue_admin_scripts($hook) {
    global $post_type;

    // Load media uploader only on 'tam_pdf' post type pages
    if ('post.php' === $hook || 'post-new.php' === $hook) {
        if ($post_type === 'tam_pdf') {
            wp_enqueue_media();
            // ❌ Remove the admin.js script if not needed
            // wp_enqueue_script('tam-admin-js', plugin_dir_url(__FILE__) . 'admin.js', array('jquery'), '1.1', true);
        }
    }
}
add_action('admin_enqueue_scripts', 'tam_enqueue_admin_scripts');

function tam_redirect_template($template) {
    if (is_singular('tam_pdf')) {
        return plugin_dir_path(__FILE__) . 'templates/template-tam-pdf.php';
    }
    return $template;
}
add_filter('single_template', 'tam_redirect_template');

//Auto-Generate slugs
function tam_update_post_slug_from_pdf($post_id) {
    if (get_post_type($post_id) !== 'tam_pdf') return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $pdf_url = isset($_POST['tam_pdf_link']) ? esc_url_raw($_POST['tam_pdf_link']) : '';
    if (empty($pdf_url)) return;

    $filename = basename($pdf_url);
    $slug = sanitize_title(preg_replace('/\.pdf$/i', '', $filename));
    $current_slug = get_post_field('post_name', $post_id);

    if ($slug && $slug !== $current_slug) {
        $unique_slug = wp_unique_post_slug($slug, $post_id, get_post_status($post_id), 'tam_pdf', 0);
        wp_update_post([
            'ID' => $post_id,
            'post_name' => $unique_slug
        ]);
    }
}
add_action('save_post', 'tam_update_post_slug_from_pdf');



// Display pdfs by Shortcode
function tam_display_pdfs($atts) {
    $GLOBALS['tam_pdf_shortcode_used'] = true;

    $atts = shortcode_atts(array('category' => ''), $atts, 'pdf');
    $category = sanitize_text_field($atts['category']);

    // Detect Polylang language
    $current_lang = function_exists('pll_current_language') ? pll_current_language() : 'en';

    $query_args = array(
        'post_type'      => 'tam_pdf',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );
    if ($category) {
        $query_args['tax_query'] = array(array(
            'taxonomy' => 'tam_category',
            'field'    => 'slug',
            'terms'    => $category,
        ));
    }

    $query = new WP_Query($query_args);
    $output = '<ul class="tam-pdfs">';

    while ($query->have_posts()) {
        $query->the_post();
        $pdf_link = get_post_meta(get_the_ID(), 'tam_pdf_link', true);
        $default_title = get_the_title();
        $date_added = get_the_date('d M Y');
        $translated_title = get_post_meta(get_the_ID(), 'tam_title_' . $current_lang, true);
        $display_title = !empty($translated_title) ? $translated_title : $default_title;

        // Get PDF size
        $pdf_size = '';
        if ($pdf_link) {
            $upload_dir = wp_upload_dir();
            
            // Extract the relative file path from the URL
            $relative_path = str_replace($upload_dir['baseurl'], '', $pdf_link);
            $pdf_path = $upload_dir['basedir'] . $relative_path;
        
            if (file_exists($pdf_path)) {
                $size_bytes = filesize($pdf_path);
                $pdf_size = tam_format_file_size($size_bytes);
            } else {
                $pdf_size = 'Unknown size';
            }
        
            $output .= '<li class="tam-pdf-item">
                <img src="' . plugin_dir_url(__FILE__) . 'assets/pdf-svgrepo-com.svg" width="25" height="25" class="pdf-icon" alt="View PDF">
                <span style="color:#000" class="tam-date">' . esc_html($date_added) . ' - </span>
                <a href="' . esc_url($pdf_link) . '" target="_blank" rel="_noopener noreferrer">' . esc_html($display_title) . '</a>
                <span style="margin-left: 10px; color: #000; font-size: 14px;">PDF, ' . esc_html($pdf_size) . '</span>
            </li>';
        }
        
    }

    wp_reset_postdata();
    $output .= '</ul>';
     

    // Add CSS for styling and spacing
    // $output .= '<style>
    //     .tam-pdfs {
    //         list-style: inside;
    //         padding: 0;
    //     }
    //     .tam-pdf-item {
    //         margin-bottom: 20px; /* Add gap between PDFs */
    //         padding: 15px;
    //         border-bottom: 1px solid #ccc;
    //     }
    //     .tam-pdf-item:last-child {
    //         border-bottom: none; /* Remove border for last item */
    //     }
    //     .tam-pdf-item img {
    //         vertical-align: middle;
    //         margin-right: 10px;
    //     }
    //     .tam-pdf-item a {
    //         text-decoration: none;
    //         color: #0073aa;
    //         font-weight: bold;
    //     }
    //     .tam-pdf-item a:hover {
    //         color: #005177;
    //     }
    // </style>';

    return $output;
}
add_shortcode('pdf', 'tam_display_pdfs');

add_action('wp_footer', function () {
    if ($GLOBALS['tam_pdf_shortcode_used']) {
        wp_enqueue_style(
            'tam-pdf-style',
            plugin_dir_url(__FILE__) . 'assets/style.css',
            array(),
            '1.0'
        );
    }
});


// Function to format file size
function tam_format_file_size($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}


// Add Category-Based Shortcodes
function tam_register_category_shortcodes() {
    $categories = get_terms(array(
        'taxonomy'   => 'tam_category',
        'hide_empty' => false,
    ));

    if (!empty($categories) && !is_wp_error($categories)) {
        foreach ($categories as $category) {
            add_shortcode('pdf_' . $category->slug, function() use ($category) {
                return tam_display_pdfs(array('category' => $category->slug));
            });
        }
    }
}
add_action('init', 'tam_register_category_shortcodes');

// Show Shortcodes in Category Edit Page
function tam_add_category_shortcode_field($term) {
    $category_slug = $term->slug;
    ?>
    <tr class="form-field">
        <th scope="row"><label for="tam_shortcode">Category Shortcodes</label></th>
        <td>
            <input type="text" id="tam_shortcode" class="widefat" value='[pdf category="<?php echo esc_attr($category_slug); ?>"]' readonly onclick="this.select();" />
            <br />
            <input type="text" id="tam_shortcode_alt" class="widefat" value='[pdf_<?php echo esc_attr($category_slug); ?>]' readonly onclick="this.select();" />
            <p class="description">Use either shortcode above to display pdfs from this category.</p>
        </td>
    </tr>
    <?php
}
add_action('tam_category_edit_form_fields', 'tam_add_category_shortcode_field');

function tam_meta_callback($post) {

      // Add nonce for security
    wp_nonce_field('tam_save_meta', 'tam_meta_nonce');
      

    $pdf_link = get_post_meta($post->ID, 'tam_pdf_link', true);
    
    // Get saved values
    $pdf_link   = esc_url(get_post_meta($post->ID, 'tam_pdf_link', true));
    $title_en   = esc_attr(get_post_meta($post->ID, 'tam_title_en', true));
    $title_mr   = esc_attr(get_post_meta($post->ID, 'tam_title_mr', true));
    $title_kok  = esc_attr(get_post_meta($post->ID, 'tam_title_kok', true));
    ?>

<button type="button" class="button tam-upload-pdf">Upload PDF</button>
<p><a href="<?php echo esc_url($pdf_link); ?>" target="_blank" id="tam_pdf_preview" <?php echo empty($pdf_link) ? 'style="display:none;"' : ''; ?>>View PDF</a></p>

    <p>
        <label for="tam_pdf_link"><?php _e('PDF Link', 'tam'); ?></label><br />
        <input type="text" id="tam_pdf_link" name="tam_pdf_link" value="<?php echo esc_attr($pdf_link); ?>" class="widefat" />
    </p>
 

    <script>
    jQuery(document).ready(function ($) {
        $('.tam-upload-pdf').click(function (e) {
            e.preventDefault();

            let mediaUploader = wp.media({
                title: 'Select PDF',
                library: { type: 'application/pdf' },
                button: { text: 'Use this PDF' },
                multiple: false
            });

            mediaUploader.on('open', function () {
                var library = mediaUploader.state().get('library');
                library.props.set('type', 'application/pdf');
            });

            mediaUploader.on('select', function () {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                var pdfUrl = attachment.url;

                $('#tam_pdf_link').val(pdfUrl);
                $('#tam_pdf_preview').attr('href', pdfUrl).text('View PDF').show();

                var fileName = attachment.filename.replace(/\.pdf$/, '').replace(/[_-]/g, ' ').trim();
                var titleField = $('#title'); // Default WordPress title field
                if (!titleField.val().trim()) {
                    titleField.val(fileName);
                     titleField.trigger('input');
                }
            });

            mediaUploader.open();
        });
    });
    </script>
    <?php
}

//Register Meta Box
function tam_add_meta_boxes() {
    add_meta_box(
        'tam_pdf_meta_box',       // ID
        __('PDF Details', 'tam'), // Title
        'tam_meta_callback',      // Callback function
        'tam_pdf',                // Post type
        'normal',                 // Context
        'high'                    // Priority
    );
}
add_action('add_meta_boxes', 'tam_add_meta_boxes');



