<?php

namespace MasterHeaderFooter;

defined('ABSPATH') || exit;

class JLTMA_CPT_Hook
{
    public static $instance = null;

    public function __construct()
    {

        add_action('admin_init', [$this, 'add_author_support_to_column'], 10);
        add_filter('manage_master_template_posts_columns', [$this, 'jltma_master_template_columns']);
        add_action('manage_master_template_posts_custom_column', [$this, 'jltma_master_template_render_column'], 10, 2);
        add_filter('parse_query', [$this, 'query_filter']);

        // Register shortcode for template rendering
        add_shortcode( 'jltma_template', [ $this, 'jltma_template_shortcode' ] );
    }

    public function add_author_support_to_column()
    {
        add_post_type_support('master_template', 'author');
    }

    /**
     * Set custom column for template list.
     */
    public function jltma_master_template_columns($columns)
    {

        $date_column = $columns['date'];
        $author_column = $columns['author'];

        unset($columns['date']);
        unset($columns['author']);

        $columns['type']      = esc_html__('Type', 'master-addons' );
        $columns['condition'] = esc_html__('Conditions', 'master-addons' );
        $columns['date']      = $date_column;
        $columns['author']    = $author_column;
        $columns['shortcode'] = esc_html__('Shortcode', 'master-addons' );

        return $columns;
    }


    public function jltma_master_template_render_column($column, $post_id)
    {
        switch ($column) {
            case 'type':

                $type = get_post_meta($post_id, 'master_template_type', true);
                $active = get_post_meta($post_id, 'master_template_activation', true);

                $display_type = empty($type) ? 'Unknown' : ucfirst($type);
                echo esc_html($display_type) . (($active == 'yes')
                    ? ('<span class="jltma-hf-status jltma-hf-status-active">' . esc_html__('Active', 'master-addons' ) . '</span>')
                    : ('<span class="jltma-hf-status jltma-hf-status-inactive">' . esc_html__('Inactive', 'master-addons' ) . '</span>'));

                break;
            case 'condition':
                $template_type = get_post_meta( get_the_id(), 'master_template_type', true);
                if($template_type === 'search' || $template_type === '404') {
                    $condition_text = 'Include &gt; ' . ucfirst($template_type);
                } else {
                    // Try to get new repeater conditions data first
                    $conditions_data = get_post_meta($post_id, 'master_template_conditions_data', true);

                    if (!empty($conditions_data) && is_array($conditions_data)) {
                    // Use new repeater format
                    $cond_parts = [];
                    foreach ($conditions_data as $condition) {
                        $type_label = ucfirst($condition['type']);
                        $rule_label = ucwords(str_replace('_', ' ', $condition['rule']));

                        $cond_text = $type_label . ' > ' . $rule_label;

                        // Handle singular conditions with post types
                        if ($condition['rule'] === 'singular') {
                            if (!empty($condition['specific'])) {
                                $post_type_obj = get_post_type_object($condition['specific']);
                                $post_type_label = $post_type_obj ? $post_type_obj->label : $condition['specific'];

                                // Check if specific posts are selected
                                if (!empty($condition['posts']) && is_array($condition['posts'])) {
                                    $post_titles = [];
                                    foreach ($condition['posts'] as $condition_post_id) {
                                        if ($condition_post_id && is_numeric($condition_post_id)) {
                                            $post_title = get_the_title($condition_post_id);
                                            if ($post_title) {
                                                $post_titles[] = $post_title;
                                            }
                                        }
                                    }

                                    if (!empty($post_titles)) {
                                        $cond_text .= ' > ' . $post_type_label . ' > ' . implode(', ', $post_titles);
                                    } else {
                                        $cond_text .= ' > All ' . $post_type_label;
                                    }
                                } else {
                                    $cond_text .= ' > All ' . $post_type_label;
                                }
                            } else {
                                $cond_text .= ' > All';
                            }
                        }
                        // Handle archive conditions
                        elseif ($condition['rule'] === 'archive') {
                            if (!empty($condition['specific'])) {
                                // Format archive type labels
                                $archive_label = $condition['specific'];
                                if ($condition['specific'] === 'category') {
                                    $archive_label = 'Category';
                                } elseif ($condition['specific'] === 'post_tag') {
                                    $archive_label = 'Tag';
                                } elseif ($condition['specific'] === 'author') {
                                    $archive_label = 'Author';
                                } elseif ($condition['specific'] === 'date') {
                                    $archive_label = 'Date';
                                } else {
                                    // For custom taxonomies
                                    $taxonomy = get_taxonomy($condition['specific']);
                                    if ($taxonomy) {
                                        $archive_label = $taxonomy->labels->singular_name;
                                    }
                                }
                                $cond_text .= ' > ' . $archive_label;
                            } else {
                                $cond_text .= ' > All';
                            }
                        }
                        // Other condition types remain as is (404, search, front_page, entire_site)

                        $cond_parts[] = $cond_text;
                    }
                    $condition_text = implode('<br>', $cond_parts);
                    } else {
                    // Fallback to legacy format
                    $cond = [
                        'jltma_hf_conditions'     => get_post_meta($post_id, 'master_template_jltma_hf_conditions', true),
                        'jltma_hfc_singular'      => get_post_meta($post_id, 'master_template_jltma_hfc_singular', true),
                        'jltma_hfc_singular_id'   => get_post_meta($post_id, 'master_template_jltma_hfc_singular_id', true),
                        'jltma_hfc_post_types_id' => get_post_meta($post_id, 'master_template_jltma_hfc_post_types_id', true),
                    ];

                    if (is_array($cond['jltma_hfc_singular_id'])) {
                        $cond['jltma_hfc_singular_id'] = implode(", ", $cond['jltma_hfc_singular_id']);
                    }

                    if (is_array($cond['jltma_hfc_post_types_id'])) {
                        $cond['jltma_hfc_post_types_id'] = $cond['jltma_hfc_post_types_id'] ;
                    }

                    // Show default conditions even if empty
                    if (empty($cond['jltma_hf_conditions'])) {
                        $condition_text = 'Include > Entire Site';
                    } else {
                        $condition_text = ucwords(str_replace(
                            '_',
                            ' ',
                            $cond['jltma_hf_conditions']
                            . (
                                ($cond['jltma_hf_conditions'] == 'singular')
                                    ? (
                                        ($cond['jltma_hfc_singular'] != '')
                                            ? (
                                                ' > ' . $cond['jltma_hfc_singular']
                                                . (
                                                    ($cond['jltma_hfc_singular_id'] != '')
                                                        ? ' > ' . $cond['jltma_hfc_singular_id']
                                                        : ''
                                                )
                                            )
                                            : ''
                                    )
                                    : (
                                        ($cond['jltma_hfc_post_types_id'] != '')
                                            ? ' > ' . $cond['jltma_hfc_post_types_id']
                                            : ''
                                    )
                            )
                        ));
                    }
                    }
                }

                echo $condition_text . '<br><a href="#" class="jltma-theme-builder-edit-cond" id="' . $post_id . '">Edit Conditions <span class="dashicons dashicons-edit"></span></a>';

                break;
            case 'shortcode':
                $shortcode = '[jltma_template id="' . $post_id . '"]';
                echo '<div style="display: flex; gap: 4px;">';
                echo '<input type="text" readonly value="' . \esc_attr($shortcode) . '" onclick="this.select()" style="flex: 1; font-family: monospace; font-size: 12px; padding: 4px 8px; border: 1px solid #ddd; border-radius: 3px; background: #f9f9f9;" title="' . \esc_attr__('Click to select', 'master-addons') . '">';
                echo '<button type="button" class="jltma-copy-shortcode" data-shortcode="' . \esc_attr($shortcode) . '" style="padding: 4px 6px; border: 1px solid #ddd; border-radius: 3px; background: #f9f9f9; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; position:absolute; right:32px;" title="' . \esc_attr__('Copy to clipboard', 'master-addons') . '">';
                echo '<span class="dashicons dashicons-clipboard" style="font-size: 14px; width: 14px; height: 14px;"></span>';
                echo '</button>';
                echo '</div>';
                break;
        }
    }


    public function  query_filter($query)
    {
        global $pagenow;
        $current_page = isset($_GET['post_type']) ? \sanitize_key($_GET['post_type']) : '';

        if (
            \is_admin()
            && 'master_template' == $current_page
            && 'edit.php' == $pagenow
            && isset($_GET['master_template_type_filter'])
            && $_GET['master_template_type_filter'] != ''
            && $_GET['master_template_type_filter'] != 'all'
        ) {
            $type = \sanitize_key($_GET['master_template_type_filter']);
            $query->query_vars['meta_key'] = 'master_template_type';
            $query->query_vars['meta_value'] = $type;
            $query->query_vars['meta_compare'] = '=';
        }
    }

    /**
     * Handle the jltma_template shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string Template content
     */
    public function jltma_template_shortcode( $atts ) {
        $atts = \shortcode_atts( [
            'id' => 0,
        ], $atts, 'jltma_template' );

        $template_id = \intval( $atts['id'] );

        if ( ! $template_id ) {
            return '';
        }


        // Check if template exists and is published
        $template_post = \get_post( $template_id );
        if ( ! $template_post || $template_post->post_status !== 'publish' || $template_post->post_type !== 'master_template' ) {
            return '';
        }



        // Use safer content rendering approach
        if ( \class_exists( '\Elementor\Plugin' ) && \did_action( 'elementor/loaded' ) ) {
            try {
                // Use output buffering to capture any content safely
                \ob_start();
                
                // Check if it's an Elementor template
                $elementor_data = \get_post_meta( $template_id, '_elementor_data', true );
                
                if ( ! empty( $elementor_data ) ) {
                    // Render using Elementor's document system
                    $document = \Elementor\Plugin::instance()->documents->get( $template_id );
                    if ( $document && $document->is_built_with_elementor() ) {
                        echo $document->get_content();
                    }
                } else {
                    // Fallback to post content
                    echo \do_shortcode( $template_post->post_content );
                }
                
                $content = \ob_get_clean();
                return $content;
                
            } catch ( \Exception $e ) {
                \ob_end_clean();
                // Return post content as fallback if Elementor fails
                return \do_shortcode( $template_post->post_content );
            }
        }

        // Fallback: Return post content if Elementor is not available
        return \do_shortcode( $template_post->post_content );
    }

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

new JLTMA_CPT_Hook();
