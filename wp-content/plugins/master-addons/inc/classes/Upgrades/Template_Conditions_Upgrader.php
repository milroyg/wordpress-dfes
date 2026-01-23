<?php

namespace MasterAddons\Upgrades;

defined('ABSPATH') || exit;

/**
 * Template Conditions Upgrader Class
 * 
 * Handles migration of existing template conditions to new repeater format
 * Adds "Include >" prefix to existing conditions and converts legacy data
 * 
 * @since 1.9.1
 */
class Template_Conditions_Upgrader
{
    /**
     * @var string Version key for tracking upgrade completion
     */
    private $upgrade_version_key = 'jltma_conditions_upgrade_v1';

    /**
     * @var array Mapping of legacy condition values to new format
     */
    private $condition_mapping = [
        'entire_site' => 'entire_site',
        'singular' => 'singular',
        'archive' => 'archive',
        'search' => 'search',
        '404' => '404',
        'front_page' => 'front_page',
        'post_types' => 'post_types',
        'product' => 'product',
        'product_archive' => 'product_archive'
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('admin_init', [$this, 'maybe_run_upgrade']);
        add_action('wp_ajax_jltma_force_conditions_upgrade', [$this, 'force_upgrade_ajax']);
    }

    /**
     * Check if upgrade needs to run and execute it
     */
    public function maybe_run_upgrade()
    {
        // Only run in admin
        if (!is_admin()) {
            return;
        }

        // Check if upgrade has already been completed
        if (get_option($this->upgrade_version_key, false)) {
            return;
        }

        // Check if we have any master_template posts that need upgrading
        if ($this->has_templates_to_upgrade()) {
            $this->run_upgrade();
        }
    }

    /**
     * Check if there are templates that need upgrading
     * 
     * @return bool
     */
    private function has_templates_to_upgrade()
    {
        global $wpdb;

        $query = $wpdb->prepare("
            SELECT COUNT(p.ID) 
            FROM {$wpdb->posts} p 
            LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
                AND pm.meta_key = 'master_template_conditions_data'
            WHERE p.post_type = %s 
            AND p.post_status = 'publish' 
            AND pm.meta_value IS NULL
        ", 'master_template');

        $count = $wpdb->get_var($query);
        return $count > 0;
    }

    /**
     * Run the upgrade process
     */
    public function run_upgrade()
    {
        global $wpdb;

        // Start transaction for data integrity
        $wpdb->query('START TRANSACTION');

        try {
            $upgraded_count = 0;
            $templates = $this->get_templates_to_upgrade();

            foreach ($templates as $template) {
                if ($this->upgrade_template_conditions($template->ID)) {
                    $upgraded_count++;
                }
            }

            // Commit transaction
            $wpdb->query('COMMIT');

            // Mark upgrade as completed
            update_option($this->upgrade_version_key, true);

            // Log upgrade completion
            // error_log("JLTMA Conditions Upgrade: Successfully upgraded {$upgraded_count} templates");
            
            // Show admin notice
            add_action('admin_notices', function() use ($upgraded_count) {
                echo '<div class="notice notice-success is-dismissible">';
                echo '<p><strong>Master Addons:</strong> Successfully upgraded ' . $upgraded_count . ' template conditions to new format.</p>';
                echo '</div>';
            });

        } catch (Exception $e) {
            // Rollback on error
            $wpdb->query('ROLLBACK');
            // error_log("JLTMA Conditions Upgrade Error: " . $e->getMessage());

            // Show error notice
            add_action('admin_notices', function() use ($e) {
                echo '<div class="notice notice-error is-dismissible">';
                echo '<p><strong>Master Addons:</strong> Failed to upgrade template conditions. Error: ' . esc_html($e->getMessage()) . '</p>';
                echo '</div>';
            });
        }
    }

    /**
     * Get templates that need upgrading
     * 
     * @return array
     */
    private function get_templates_to_upgrade()
    {
        global $wpdb;

        return $wpdb->get_results($wpdb->prepare("
            SELECT p.ID, p.post_title
            FROM {$wpdb->posts} p 
            LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
                AND pm.meta_key = 'master_template_conditions_data'
            WHERE p.post_type = %s 
            AND p.post_status = 'publish' 
            AND pm.meta_value IS NULL
            ORDER BY p.ID ASC
        ", 'master_template'));
    }

    /**
     * Upgrade individual template conditions
     * 
     * @param int $template_id
     * @return bool
     */
    private function upgrade_template_conditions($template_id)
    {
        // Get existing legacy condition data
        $legacy_conditions = [
            'jltma_hf_conditions' => get_post_meta($template_id, 'master_template_jltma_hf_conditions', true),
            'jltma_hfc_singular' => get_post_meta($template_id, 'master_template_jltma_hfc_singular', true),
            'jltma_hfc_singular_id' => get_post_meta($template_id, 'master_template_jltma_hfc_singular_id', true),
            'jltma_hfc_post_types_id' => get_post_meta($template_id, 'master_template_jltma_hfc_post_types_id', true),
        ];

        // Convert to new repeater format
        $new_conditions = $this->convert_legacy_to_repeater($legacy_conditions);

        // Save new conditions data
        if (!empty($new_conditions)) {
            update_post_meta($template_id, 'master_template_conditions_data', $new_conditions);
            return true;
        }

        return false;
    }

    /**
     * Convert legacy conditions to new repeater format
     * 
     * @param array $legacy_conditions
     * @return array
     */
    private function convert_legacy_to_repeater($legacy_conditions)
    {
        $conditions_data = [];

        // Get the main condition
        $main_condition = $legacy_conditions['jltma_hf_conditions'];

        // If no main condition, set default
        if (empty($main_condition)) {
            $conditions_data[] = [
                'type' => 'include',
                'rule' => 'entire_site',
                'specific' => ''
            ];
            return $conditions_data;
        }

        // Map legacy condition to new format
        $rule = isset($this->condition_mapping[$main_condition]) 
            ? $this->condition_mapping[$main_condition] 
            : $main_condition;

        $condition = [
            'type' => 'include', // Always default to include for existing conditions
            'rule' => $rule,
            'specific' => ''
        ];

        // Handle singular conditions with specific targeting
        if ($main_condition === 'singular') {
            $singular_type = $legacy_conditions['jltma_hfc_singular'];
            $singular_ids = $legacy_conditions['jltma_hfc_singular_id'];

            if ($singular_type === 'selective' && !empty($singular_ids)) {
                $condition['specific'] = $singular_ids;
            } elseif (!empty($singular_type) && $singular_type !== 'all') {
                // Handle other singular types like 'front_page', 'all_posts', etc.
                $condition['rule'] = $singular_type;
            }
        }

        // Handle post types conditions
        if ($main_condition === 'post_types') {
            $post_types = $legacy_conditions['jltma_hfc_post_types_id'];
            if (!empty($post_types)) {
                $condition['specific'] = is_array($post_types) ? implode(', ', $post_types) : $post_types;
            }
        }

        $conditions_data[] = $condition;

        return $conditions_data;
    }

    /**
     * Force upgrade via AJAX (for admin manual trigger)
     */
    public function force_upgrade_ajax()
    {
        // Verify nonce and permissions
        if (!check_ajax_referer('jltma_force_upgrade', 'nonce', false) || !current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied']);
        }

        // Reset upgrade flag and run upgrade
        delete_option($this->upgrade_version_key);
        
        if ($this->has_templates_to_upgrade()) {
            $this->run_upgrade();
            wp_send_json_success(['message' => 'Upgrade completed successfully']);
        } else {
            wp_send_json_success(['message' => 'No templates to upgrade']);
        }
    }

    /**
     * Get upgrade status for admin display
     * 
     * @return array
     */
    public function get_upgrade_status()
    {
        return [
            'completed' => get_option($this->upgrade_version_key, false),
            'templates_to_upgrade' => $this->has_templates_to_upgrade() ? $this->count_templates_to_upgrade() : 0
        ];
    }

    /**
     * Count templates that need upgrading
     * 
     * @return int
     */
    private function count_templates_to_upgrade()
    {
        global $wpdb;

        return (int) $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(p.ID) 
            FROM {$wpdb->posts} p 
            LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
                AND pm.meta_key = 'master_template_conditions_data'
            WHERE p.post_type = %s 
            AND p.post_status = 'publish' 
            AND pm.meta_value IS NULL
        ", 'master_template'));
    }

    /**
     * Add admin menu for upgrade management (optional)
     */
    public function add_upgrade_admin_page()
    {
        add_submenu_page(
            'edit.php?post_type=master_template',
            'Template Conditions Upgrade',
            'Upgrade Conditions',
            'manage_options',
            'jltma-upgrade-conditions',
            [$this, 'render_upgrade_page']
        );
    }

    /**
     * Render upgrade admin page
     */
    public function render_upgrade_page()
    {
        $status = $this->get_upgrade_status();
        ?>
        <div class="wrap">
            <h1>Template Conditions Upgrade</h1>
            
            <?php if ($status['completed']): ?>
                <div class="notice notice-success">
                    <p><strong>Upgrade Completed:</strong> All template conditions have been upgraded to the new format.</p>
                </div>
            <?php else: ?>
                <div class="notice notice-warning">
                    <p><strong>Upgrade Pending:</strong> Found <?php echo $status['templates_to_upgrade']; ?> templates that need upgrading.</p>
                </div>
            <?php endif; ?>

            <div class="card">
                <h2>About This Upgrade</h2>
                <p>This upgrade converts existing template conditions to the new repeater format with the following improvements:</p>
                <ul>
                    <li>✅ Adds "Include >" prefix to all existing conditions</li>
                    <li>✅ Converts legacy condition data to new repeater format</li>
                    <li>✅ Maintains backward compatibility with existing templates</li>
                    <li>✅ Improves condition display in admin columns</li>
                </ul>

                <?php if (!$status['completed'] && $status['templates_to_upgrade'] > 0): ?>
                    <button id="force-upgrade" class="button button-primary">
                        Force Run Upgrade Now
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('#force-upgrade').on('click', function() {
                var btn = $(this);
                btn.prop('disabled', true).text('Running Upgrade...');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'jltma_force_conditions_upgrade',
                        nonce: '<?php echo wp_create_nonce('jltma_force_upgrade'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Upgrade failed: ' + response.data.message);
                            btn.prop('disabled', false).text('Force Run Upgrade Now');
                        }
                    },
                    error: function() {
                        alert('Upgrade failed due to network error');
                        btn.prop('disabled', false).text('Force Run Upgrade Now');
                    }
                });
            });
        });
        </script>
        <?php
    }

    /**
     * Static method to get instance
     * 
     * @return Template_Conditions_Upgrader
     */
    public static function instance()
    {
        static $instance = null;
        if (is_null($instance)) {
            $instance = new self();
        }
        return $instance;
    }
}

// Initialize the upgrader
Template_Conditions_Upgrader::instance();