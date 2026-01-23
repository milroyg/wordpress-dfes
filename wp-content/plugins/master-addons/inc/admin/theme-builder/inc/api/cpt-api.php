<?php

namespace MasterHeaderFooter;

class JLTMA_Header_Footer_CPT_API extends JLTMA_Header_Footer_Rest_API
{
    public function __construct()
    {
        $this->config("ma-template", "/(?P<id>\w+)/");
        $this->init();
        
        // Register additional endpoints
        add_action('rest_api_init', function() {
            register_rest_route('masteraddons/v2/ma-template', '/post-types', array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [$this, 'get_post_types'],
                'permission_callback' => function() {
                    return current_user_can('edit_posts');
                }
            ));

            register_rest_route('masteraddons/v2/ma-template', '/archive-types', array(
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [$this, 'get_archive_types'],
                'permission_callback' => function() {
                    return current_user_can('edit_posts');
                }
            ));
        });
    }

    public function get_update()
    {
        if (!current_user_can('edit_posts')) {
            return;
        }

        $id = $this->request['id'];
        $open_editor = $this->request['open_editor'];

        $title = ($this->request['title'] == '') ? ('Master Addons Template #' . time()) : $this->request['title'];
        $activation = isset($this->request['activation']) ? $this->request['activation'] : '';
        $type = isset($this->request['type']) ? $this->request['type'] : 'header';
        
        

        // Handle new repeater-based conditions
        $condition_types = isset($this->request['jltma_condition_type']) ? (array)$this->request['jltma_condition_type'] : [];
        $condition_rules = isset($this->request['jltma_condition_rule']) ? (array)$this->request['jltma_condition_rule'] : [];
        $condition_specifics = isset($this->request['jltma_condition_specific']) ? (array)$this->request['jltma_condition_specific'] : [];
        $condition_posts = isset($this->request['jltma_condition_posts']) ? (array)$this->request['jltma_condition_posts'] : [];

        // Process new repeater conditions
        $conditions_data = [];
        if (!empty($condition_types) && !empty($condition_rules)) {
            $has_include = false;
            $include_entire_site = false;
            $has_other_includes = false;
            
            for ($i = 0; $i < count($condition_types); $i++) {
                if (isset($condition_types[$i]) && isset($condition_rules[$i])) {
                    $condition_type = sanitize_text_field($condition_types[$i]);
                    $condition_rule = sanitize_text_field($condition_rules[$i]);
                    $condition_specific = isset($condition_specifics[$i]) ? sanitize_text_field($condition_specifics[$i]) : '';
                    
                    // Handle selected posts for this condition
                    $selected_posts = [];
                    if (isset($condition_posts[$i]) && is_array($condition_posts[$i])) {
                        $selected_posts = array_map('intval', array_filter($condition_posts[$i]));
                    }
                    
                    // Validate that specific fields are provided when required
                    if (($condition_rule === 'singular' || $condition_rule === 'archive') && empty($condition_specific)) {
                        // Allow empty specific for "All" option
                        $condition_specific = '';
                    }
                    
                    $conditions_data[] = [
                        'type' => $condition_type,
                        'rule' => $condition_rule,
                        'specific' => $condition_specific,
                        'posts' => $selected_posts
                    ];
                    
                    // Track include conditions for validation
                    if ($condition_type === 'include') {
                        $has_include = true;
                        if ($condition_rule === 'entire_site') {
                            $include_entire_site = true;
                        } else {
                            $has_other_includes = true;
                        }
                    }
                }
            }
            
            // Backend validation - ensure at least one include condition
            if (!$has_include) {
                return new \WP_Error('validation_error', 'At least one "Include" condition is required.', array('status' => 400));
            }
            
            // Backend validation - check for conflicting entire site with other includes
            if ($include_entire_site && $has_other_includes) {
                return new \WP_Error('validation_error', 'You cannot use "Entire Site" with other include conditions.', array('status' => 400));
            }
            
            // Check for conflicts with existing templates
            $conflict_check = $this->check_template_conflicts($conditions_data, $id, $type);
            if (is_wp_error($conflict_check)) {
                return $conflict_check;
            }
        }

        // Fallback to legacy format for backward compatibility
        $jltma_hf_conditions        = ($type == 'section') ? '' : $this->request['jltma_hf_conditions'];
        $jltma_hfc_singular         = ($type == 'section') ? '' : $this->request['jltma_hfc_singular'];
        $jltma_hfc_singular_id      = ($type == 'section') ? '' : (array)$this->request['jltma_hfc_singular_id'];
        $jltma_hfc_post_types_id      = ($type == 'section') ? '' : (array)$this->request['jltma_hfc_post_types_id'];

        // If we have new repeater data, use it; otherwise fall back to legacy
        if (!empty($conditions_data)) {
            // Use first condition for legacy compatibility
            $primary_condition = $conditions_data[0];
            $jltma_hf_conditions = $primary_condition['rule'];
            
            if ($primary_condition['rule'] === 'singular' && !empty($primary_condition['specific'])) {
                $jltma_hfc_singular = 'selective';
                $jltma_hfc_singular_id = [$primary_condition['specific']];
                $jltma_hfc_post_types_id = [];
            } elseif ($primary_condition['rule'] === 'singular') {
                $jltma_hfc_singular = 'all';
                $jltma_hfc_singular_id = [];
                $jltma_hfc_post_types_id = [];
            } elseif ($primary_condition['rule'] === 'archive' && !empty($primary_condition['specific'])) {
                // Handle archive conditions
                $jltma_hfc_singular = '';
                $jltma_hfc_singular_id = [];
                $jltma_hfc_post_types_id = [$primary_condition['specific']];
            } else {
                $jltma_hfc_singular = '';
                $jltma_hfc_singular_id = [];
                $jltma_hfc_post_types_id = [];
            }
        }

        $post_data = array(
            'post_title'    => $title,
            'post_status'   => 'publish',
            'post_type'     => 'master_template',
        );

        $post = get_post($id);

        if ($post == null) {
            $id = wp_insert_post($post_data);
        } else {
            $post_data['ID'] = $id;
            wp_update_post($post_data);
        }

        update_post_meta($id, '_wp_page_template', 'elementor_canvas');
        update_post_meta($id, 'master_template_activation', $activation);
        update_post_meta($id, 'master_template_type', $type);
        update_post_meta($id, 'master_template_jltma_hf_conditions', $jltma_hf_conditions);
        update_post_meta($id, 'master_template_jltma_hfc_singular', $jltma_hfc_singular);
        update_post_meta($id, 'master_template_jltma_hfc_singular_id', implode(", ", $jltma_hfc_singular_id));
        update_post_meta($id, 'master_template_jltma_hfc_post_types_id', implode(", ", $jltma_hfc_post_types_id));
        
        // Store new repeater conditions data - always store something
        if (empty($conditions_data)) {
            // If no conditions were submitted, create a default "Include > Entire Site" condition
            $conditions_data = [
                [
                    'type' => 'include',
                    'rule' => 'entire_site',
                    'specific' => '',
                    'posts' => []
                ]
            ];
        }
        update_post_meta($id, 'master_template_conditions_data', $conditions_data);

        if ($open_editor == 'true') {
            $url = get_admin_url() . '/post.php?post=' . $id . '&action=elementor';
            wp_redirect($url);
            exit;
        } else {
            
            // Generate condition text from new repeater data if available
            if (!empty($conditions_data)) {
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
                                foreach ($condition['posts'] as $post_id) {
                                    if ($post_id && is_numeric($post_id)) {
                                        $post_title = get_the_title($post_id);
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
                $cond = implode('<br>', $cond_parts);
            } else {
                // Fallback to legacy format
                $cond = ucwords(str_replace(
                    '_',
                    ' ',
                    $jltma_hf_conditions
                        . (
                            ($jltma_hf_conditions == 'singular')
                                ? (
                                    ($jltma_hfc_singular != '')
                                        ? (
                                            ' > ' . $jltma_hfc_singular
                                            . (
                                                ($jltma_hfc_singular_id != '')
                                                    ? ' > ' . implode(", ", $jltma_hfc_singular_id)
                                                    : ''
                                            )
                                        )
                                        : ''
                                )
                                : (
                                    ($jltma_hfc_post_types_id != '')
                                        ? ' > ' . implode(", ", $jltma_hfc_post_types_id)
                                        : ''
                                )
                        )
                ));
            }

            // Add edit icon to condition text for admin display
            $cond_with_edit = $cond . '<br><a href="#" class="jltma-theme-builder-edit-cond" id="' . $id . '">Edit Conditions <span class="dashicons dashicons-edit"></span></a>';


            return [
                'saved' => true,
                'data' => [
                    'id' => $id,
                    'title' => $title,
                    'type' => $type,
                    'activation' => $activation,
                    'cond_text' => $cond_with_edit,
                    'type_html' => (ucfirst($type) . (($activation == 'yes')
                        ? ('<span class="jltma-hf-status jltma-hf-status-active">' . esc_html__('Active', 'master-addons' ) . '</span>')
                        : ('<span class="jltma-hf-status jltma-hf-status-inactive">' . esc_html__('Inactive', 'master-addons' ) . '</span>'))),
                ]
            ];
        }
    }

    public function get_get()
    {
        $id = $this->request['id'];

        // Security check: Verify user has permission to edit posts
        if (!current_user_can('edit_posts')) {
            return new \WP_Error('rest_forbidden', 'You do not have permission to access this resource.', array('status' => 403));
        }

        $post = get_post($id);

        // Verify post exists
        if ($post === null) {
            return new \WP_Error('rest_not_found', 'Template not found.', array('status' => 404));
        }

        // Verify post type is correct
        if ($post->post_type !== 'master_template') {
            return new \WP_Error('rest_forbidden', 'Invalid post type.', array('status' => 403));
        }

        // Additional security check: Verify user can edit this specific post
        if (!current_user_can('edit_post', $id)) {
            return new \WP_Error('rest_forbidden', 'You do not have permission to access this template.', array('status' => 403));
        }

        $conditions_data = get_post_meta($post->ID, 'master_template_conditions_data', true);

        return [
            'title'                 => $post->post_title,
            'status'                => $post->post_status,
            'activation'            => get_post_meta($post->ID, 'master_template_activation', true),
            'type'                  => get_post_meta($post->ID, 'master_template_type', true),
            'jltma_hf_conditions'   => get_post_meta($post->ID, 'master_template_jltma_hf_conditions', true),
            'jltma_hfc_singular'    => get_post_meta($post->ID, 'master_template_jltma_hfc_singular', true),
            'jltma_hfc_singular_id' => get_post_meta($post->ID, 'master_template_jltma_hfc_singular_id', true),
            'jltma_hfc_post_types_id' => get_post_meta($post->ID, 'master_template_jltma_hfc_post_types_id', true),
            'conditions_data'       => $conditions_data ?: [],
        ];
    }

    public function get_post_types()
    {
        if (!current_user_can('edit_posts')) {
            return new \WP_Error('rest_forbidden', 'You do not have permission to access post types.', array('status' => 403));
        }

        $post_types = get_post_types(array('public' => true), 'objects');
        $options = array();

        foreach ($post_types as $post_type) {
            // Skip attachment, master_template, and mastermega_content post types as they're not relevant for templates
            if (in_array($post_type->name, array('attachment', 'master_template', 'mastermega_content'))) {
                continue;
            }

            $options[] = array(
                'value' => $post_type->name,
                'label' => $post_type->label
            );
        }

        return $options;
    }

    public function get_archive_types()
    {
        if (!current_user_can('edit_posts')) {
            return new \WP_Error('rest_forbidden', 'You do not have permission to access archive types.', array('status' => 403));
        }

        $archive_types = array();

        // Add built-in archive types
        $archive_types[] = array('value' => 'author', 'label' => 'Author Archive');
        $archive_types[] = array('value' => 'date', 'label' => 'Date Archive');

        // Get all public taxonomies
        $taxonomies = get_taxonomies(array('public' => true), 'objects');
        
        foreach ($taxonomies as $taxonomy) {
            // Skip post formats and nav menu categories
            if (in_array($taxonomy->name, array('post_format', 'nav_menu'))) {
                continue;
            }

            $label = $taxonomy->label;
            if ($taxonomy->name === 'category') {
                $label = 'Category Archive';
            } elseif ($taxonomy->name === 'post_tag') {
                $label = 'Tag Archive';
            } elseif ($taxonomy->name === 'product_cat') {
                $label = 'Product Category';
            } elseif ($taxonomy->name === 'product_tag') {
                $label = 'Product Tag';
            } else {
                $label = $taxonomy->label . ' Archive';
            }

            $archive_types[] = array(
                'value' => $taxonomy->name,
                'label' => $label
            );
        }

        return $archive_types;
    }

    /**
     * Check for template conflicts with existing templates
     * 
     * @param array $conditions_data
     * @param int $current_id
     * @param string $current_type
     * @return bool|\WP_Error
     */
    private function check_template_conflicts($conditions_data, $current_id, $current_type) {
        // Get all existing active templates
        $existing_templates = get_posts(array(
            'post_type' => 'master_template',
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'master_template_activation',
                    'value' => 'yes',
                    'compare' => '='
                )
            ),
            'posts_per_page' => -1,
            'exclude' => array($current_id) // Exclude current template being edited
        ));

        foreach ($existing_templates as $existing_template) {
            $existing_type = get_post_meta($existing_template->ID, 'master_template_type', true);
            $existing_conditions = get_post_meta($existing_template->ID, 'master_template_conditions_data', true);
            
            // Only check templates of the same type (header, footer, etc.)
            if ($existing_type !== $current_type) {
                continue;
            }
            
            // Check if conditions overlap
            if ($this->conditions_overlap($conditions_data, $existing_conditions ?: array())) {
                $conflict_message = sprintf(
                    'This template conflicts with existing template "%s" (ID: %d). Templates of the same type cannot have overlapping conditions.',
                    $existing_template->post_title,
                    $existing_template->ID
                );
                return new \WP_Error('template_conflict', $conflict_message, array('status' => 400));
            }
        }

        return true;
    }

    /**
     * Check if two sets of conditions overlap
     * 
     * @param array $conditions1
     * @param array $conditions2
     * @return bool
     */
    private function conditions_overlap($conditions1, $conditions2) {
        if (empty($conditions1) || empty($conditions2)) {
            return false;
        }

        // Extract include and exclude conditions from both sets
        $includes1 = array_filter($conditions1, function($c) { return $c['type'] === 'include'; });
        $includes2 = array_filter($conditions2, function($c) { return $c['type'] === 'include'; });
        $excludes1 = array_filter($conditions1, function($c) { return $c['type'] === 'exclude'; });
        $excludes2 = array_filter($conditions2, function($c) { return $c['type'] === 'exclude'; });

        // Check if conditions1 has "entire_site"
        $has_entire_site1 = false;
        foreach ($includes1 as $condition) {
            if ($condition['rule'] === 'entire_site') {
                $has_entire_site1 = true;
                break;
            }
        }

        // Check if conditions2 has "entire_site"
        $has_entire_site2 = false;
        foreach ($includes2 as $condition) {
            if ($condition['rule'] === 'entire_site') {
                $has_entire_site2 = true;
                break;
            }
        }

        // If conditions1 has "entire_site", check if any include from conditions2 is NOT excluded in conditions1
        if ($has_entire_site1) {
            foreach ($includes2 as $condition2) {
                // Skip "entire_site" check (they both have it, handled separately)
                if ($condition2['rule'] === 'entire_site') {
                    continue;
                }

                // Check if this condition is excluded in conditions1
                $is_excluded = false;
                foreach ($excludes1 as $exclude) {
                    if ($this->single_conditions_overlap($condition2, $exclude)) {
                        $is_excluded = true;
                        break;
                    }
                }
                // If not excluded, then they overlap
                if (!$is_excluded) {
                    return true;
                }
            }
            // If conditions2 only has entire_site or all its includes are excluded, check if both have entire_site
            if ($has_entire_site2) {
                return true; // Both have entire_site
            }
            return false; // All specific includes from conditions2 are excluded in conditions1
        }

        // If conditions2 has "entire_site", check if any include from conditions1 is NOT excluded in conditions2
        if ($has_entire_site2) {
            foreach ($includes1 as $condition1) {
                // Skip "entire_site" check (already handled above)
                if ($condition1['rule'] === 'entire_site') {
                    continue;
                }

                // Check if this condition is excluded in conditions2
                $is_excluded = false;
                foreach ($excludes2 as $exclude) {
                    if ($this->single_conditions_overlap($condition1, $exclude)) {
                        $is_excluded = true;
                        break;
                    }
                }
                // If not excluded, then they overlap
                if (!$is_excluded) {
                    return true;
                }
            }
            return false; // All includes from conditions1 are excluded in conditions2
        }

        // No "entire_site" involved, check for specific rule overlaps
        foreach ($includes1 as $condition1) {
            foreach ($includes2 as $condition2) {
                if ($this->single_conditions_overlap($condition1, $condition2)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if two individual conditions overlap
     * 
     * @param array $condition1
     * @param array $condition2
     * @return bool
     */
    private function single_conditions_overlap($condition1, $condition2) {
        // Same rule type
        if ($condition1['rule'] === $condition2['rule']) {
            // If both have no specific value, they overlap
            if (empty($condition1['specific']) && empty($condition2['specific'])) {
                return true;
            }
            // If one has no specific and other has specific, they overlap
            if (empty($condition1['specific']) || empty($condition2['specific'])) {
                return true;
            }
            // If both have same specific value, they overlap
            if ($condition1['specific'] === $condition2['specific']) {
                return true;
            }
        }

        return false;
    }
}

new JLTMA_Header_Footer_CPT_API();