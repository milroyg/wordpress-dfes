<?php

namespace MasterAddons\Inc\Classes;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use MasterAddons\Inc\Classes\Helper;

/**
 * Freemius_Hooks
 *
 * @author Jewel Theme <support@jeweltheme.com>
 */
if (!class_exists('MasterAddons\Inc\Classes\Freemius_Hooks')) {
  class Freemius_Hooks
  {
  
    private static $instance = null;

    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct() {

      // Reorder admin submenu items to place Freemius menus at the end
      add_action( 'admin_menu', [$this, 'jltma_reorder_admin_submenu'], 999 );

      add_action( 'wp_head', [$this, 'jltma_add_licensing_helper'] );
      ma_el_fs()->add_filter('connect_message_on_update', [$this, 'jltma_custom_connect_message_on_update'], 10, 6);
      
      // Not like register_uninstall_hook(), you do NOT have to use a static function.
      ma_el_fs()->add_action('after_uninstall', [$this, 'jltma_uninstall_cleanup']);

      ma_el_fs()->add_filter( 'permissions_list', [$this, 'jltma_add_helpscount_permission'] );
      
      //Controlling the visibility of admin notices added by the Freemius SDK
      ma_el_fs()->add_filter( 'show_admin_notice', [$this, 'jltma_custom_show_admin_notice'], 10, 2 );

      // Freemius Purchase Completion JavaScript Callback Filter
      ma_el_fs()->add_filter('checkout/purchaseCompleted', [$this, 'jltma_after_purchase_js'] );

      // Freemius submenu items visibility filter
      ma_el_fs()->add_filter( 'is_submenu_visible', [$this, 'jltma_is_submenu_visible'], 10, 2 );

      ma_el_fs()->add_filter( 'show_affiliate_program_notice', '__return_false' );

      ma_el_fs()->add_filter( 'is_submenu_visible', [$this, 'jltma_disable_contact_for_free_users'], 10, 2 );

      ma_el_fs()->add_filter( 'show_deactivation_subscription_cancellation', '__return_false' );

      // Disable Freemius deactivation feedback popup (we have our own custom feedback)
      ma_el_fs()->add_filter( 'show_deactivation_feedback_form', '__return_false' );

      // Support menu and URL customization
      ma_el_fs()->add_filter( 'support_forum_submenu', [$this, 'jltma_override_support_menu_text'] );
      ma_el_fs()->add_filter( 'support_forum_url', [$this, 'jltma_support_forum_url'] );
      ma_el_fs()->add_filter( 'plugin_icon', [$this, 'jltma_freemius_logo_icon'] );

      // Premium version activation hook
      ma_el_fs()->add_action( 'after_premium_version_activation', [$this, 'jltma_after_premium_activation'] );

      // Remove Freemius links from free plugin when Pro is active
      if ( Helper::jltma_premium() ) {
        add_filter( 'plugin_action_links_' . JLTMA_BASE, [$this, 'jltma_remove_freemius_action_links'], 999 );
      }

      // Add plugin row meta links + deduplicate with Freemius's own
      // Support / Documentation entries (always, regardless of Pro).
      // Our branded entries with dashicons ship first via jltma_plugin_row_meta;
      // jltma_remove_freemius_row_meta runs at priority 999 to strip
      // the duplicate plain-text links Freemius injects afterwards.
      add_filter( 'plugin_row_meta', [$this, 'jltma_plugin_row_meta'], 10, 2 );
      add_filter( 'plugin_row_meta', [$this, 'jltma_remove_freemius_row_meta'], 999, 2 );

      // Trial
      // ma_el_fs()->override_i18n( array(
      //   'hey'                                        => 'Hey',
      //   'trial-x-promotion-message'                  => 'Thank you so much for using %s!',
      //   'already-opted-in-to-product-usage-tracking' => 'How do you like %s so far? Test all our %s premium features with a %d-day free trial.',
      //   'start-free-trial'                           => 'Start free trial',
      //   // Trial with a payment method required.
      //   'no-commitment-for-x-days'                   => 'No commitment for %s days - cancel anytime!',
      //   // Trial without a payment method.
      //   'no-cc-required'                             => 'No credit card required',
      // ) );


      // Show the 1st trial promotion after 7 days instead of 24 hours.
      // ma_el_fs()->add_filter( 'show_first_trial_after_n_sec', [$this, 'jltma_show_first_trial_after_7_days'] );
      
      // Re-show the trial promotional offer after every 60 days instead of 30 days.
      // ma_el_fs()->add_filter( 'reshow_trial_after_every_n_sec', [$this, 'jltma_reshow_trial_after_every_60_days'] );
    }


    public 	function jltma_disable_contact_for_free_users( $is_visible, $menu_id ) {
      if ( 'contact' != $menu_id ) {
        return $is_visible;
      }
      return false;
    }

    public function jltma_add_licensing_helper(){
      ?>
      <script type="text/javascript">
        (function () {
          window.ma_el_fs = { can_use_premium_code__premium_only: <?php
            echo  json_encode( function_exists('ma_el_fs') && ma_el_fs()->can_use_premium_code() ) ;
            ?>};
        })();
      </script>
    <?php
    }

    // Customize Opt-in Message for Existing Users
    public function jltma_custom_connect_message_on_update( $message, $user_first_name, $plugin_title, $user_login, $site_link, $freemius_link ){
      
      return sprintf(
          /* translators: 1: First Name, 2: Plugin Title, 3: Freemius Link. */
          __( 'Hey %1$s,<br> Please help us improve %2$s! If you opt-in, some data about your usage of %2$s will be sent to %3$s. If you skip this, that\'s okay! %2$s will still work just fine.', 'master-addons' ),
          $user_first_name,
          '<b>' . $plugin_title . '</b>',
          $freemius_link
      );
    }

    /**
     * Cleanup plugin data on uninstall
     * Called by Freemius after_uninstall hook
     */
    public function jltma_uninstall_cleanup(){
      // global $wpdb;

      // // Delete all plugin options
      // delete_option('maad_el_save_settings');
      // delete_option('ma_el_extensions_save_settings');
      // delete_option('ma_el_third_party_plugins_save_settings');
      // delete_option('jltma_icons_library_save_settings');
      // delete_option('jltma_white_label_settings');
      // delete_option('_master_addons_version');
      // delete_option('jltma_sheet_promo_data');
      // delete_option('jltma_sheet_promo_data_hash');
      // delete_option('jltma_activation_time');

      // // Delete custom post types (Theme Builder, Popup Builder, Widget Builder, etc.)
      // $custom_post_types = array(
      // 	'jltma_template',     // Theme Builder templates
      // 	'jltma-popup',        // Popup Builder
      // 	'jltma-widget'        // Widget Builder
      // );

      // foreach ($custom_post_types as $post_type) {
      // 	$posts = get_posts(array(
      // 		'post_type'      => $post_type,
      // 		'posts_per_page' => -1,
      // 		'post_status'    => 'any'
      // 	));

      // 	foreach ($posts as $post) {
      // 		wp_delete_post($post->ID, true); // true = force delete, bypass trash
      // 	}
      // }

      // // Delete all transients
      // $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_master-addons%'");
      // $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_master-addons%'");
      // $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_jltma%'");
      // $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_jltma%'");

      // // Clear scheduled cron jobs
      // wp_clear_scheduled_hook('jltma_sheet_promo_data_remote_sync');

      // // Drop popup builder custom table if it exists
      // $table_name = $wpdb->prefix . 'jltma_popup_builder';
      // $wpdb->query("DROP TABLE IF EXISTS {$table_name}");

      // // Delete post meta related to plugin
      // $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'jltma%'");
      // $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_jltma%'");
      // $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'ma_el%'");

      // // Delete user meta related to plugin
      // $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'jltma%'");
      // $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'ma_el%'");

      // // Clear any rewrite rules
      // flush_rewrite_rules();
    }

    public function jltma_add_helpscount_permission( $permissions ){
      $permissions['helpscout'] = array(
        'icon-class' => 'dashicons dashicons-email-alt',
        'label'      => ma_el_fs()->get_text_inline( 'Help Scout', 'helpscout' ),
        'desc'       => ma_el_fs()->get_text_inline( 'Rendering Help Scout\'s beacon for easy support access', 'permissions-helpscout' ),
        'priority'   => 16,
      );

      $permissions['newsletter'] = array(
        'icon-class' => 'dashicons dashicons-email-alt',
        'label'      => ma_el_fs()->get_text_inline( 'Newsletter', 'permissions-newsletter' ),
        'desc'       => ma_el_fs()->get_text_inline( 'Updates, announcements, marketing, no spam', 'permissions-newsletter_desc' ),
        'priority'   => 15,
      );
    }

    
    public function jltma_custom_show_admin_notice( $show, $msg ) {
      if ('trial_promotion' == $msg['id']) {
        // Don't show the trial promotional admin notice.
        return false;
      }
      return $show;
    }

    public function jltma_after_purchase_js( $js_function ) {
		  return 'function (data) {
        console.log("checkout", "purchaseCompleted");
      }';
    }

    public function jltma_is_submenu_visible($is_visible, $submenu_id){
        // Hide account page if user is not registered with Freemius
        if ('account' === $submenu_id && !ma_el_fs()->is_registered()) {
            return false;
        }
        // Hide pricing page for premium users
        if ('pricing' === $submenu_id && Helper::jltma_premium()) {
            return false;
        }
        return $is_visible;
    }

    /**
     * Remove account page when not premium - show for licensed users
     */
    public function jltma_remove_account_page_if_unregistered() {
        if (!Helper::jltma_premium()) {
            remove_submenu_page( 'master-addons-settings', 'master-addons-account' );
        }
    }


    public function jltma_show_first_trial_after_7_days( $day_in_sec ) {
      // 7 days in sec.
      return 7 * 24 * 60 * 60;
    }


    public function jltma_reshow_trial_after_every_60_days( $thirty_days_in_sec ) {
      // 60 days in sec.
		  return 60 * 24 * 60 * 60;
    }

    /**
     * Override support menu text
     *
     * @return string
     */
    public function jltma_override_support_menu_text() {
      return __( 'Support', 'master-addons' );
    }

    /**
     * Support Forum URL
     *
     * @param string $support_url Default support URL
     * @return string Modified support URL
     */
    public function jltma_support_forum_url( $support_url ) {
			if (Helper::jltma_premium()) {
				$support_url = 'https://master-addons.com/contact-us/';
			} else {
				$support_url = 'https://wordpress.org/support/plugin/master-addons/#new-topic-0';
			}
			return $support_url;      
    }

    /**
     * Freemius plugin icon
     *
     * @return string Path to plugin icon
     */
    public function jltma_freemius_logo_icon() {
      return JLTMA_PATH . 'assets/images/master-addons.png';
    }

    /**
     * After premium version activation
     * Redirect to welcome page on single site
     *
     * @param bool $network_wide Whether activated network-wide
     */
    public function jltma_after_premium_activation( $network_wide ) {
      if ( function_exists( 'is_multisite' ) && is_multisite() ) {
        // Do nothing for multisite
      } else {
        // Redirect to welcome page
        set_transient( '_master_addons_activation_redirect', true, 30 );
      }
    }

    /**
     * Add plugin row meta links
     *
     * @param array  $links Existing row meta links
     * @param string $file  Plugin file
     * @return array Modified links
     */
    public function jltma_plugin_row_meta( $links, $file ) {
      if ( JLTMA_BASE === $file ) {
        // Array keys are prefixed with jltma_ so they never collide
        // with Freemius's own 'support' / 'documentation' / 'pricing'
        // keys that jltma_remove_freemius_row_meta drops.
        //
        // Inline styles brand-color the links on the Plugins list
        // (blue for Docs/Support, purple for Premium) because this
        // runs on the core plugins.php screen where our admin CSS
        // isn't enqueued — inline is the safe, self-contained path.
        $link_style    = 'style="color:#2563eb;font-weight:600;"';
        $premium_style = 'style="color:#6814cd;font-weight:700;"';
        $icon_style    = 'style="vertical-align:middle;margin-right:3px;font-size:16px;width:16px;height:16px;"';

        $new_links = array(
          'jltma_doc'     => '<a ' . $link_style . ' href="' . esc_url( 'https://master-addons.com/docs/' ) . '" target="_blank"><span class="dashicons dashicons-media-document" ' . $icon_style . '></span>Docs</a>',
          'jltma_support' => '<a ' . $link_style . ' href="' . esc_url( 'https://master-addons.com/contact-us' ) . '" target="_blank"><span class="dashicons dashicons-admin-users" ' . $icon_style . '></span>Support</a>',
          'jltma_pro'     => '<a ' . $premium_style . ' href="' . esc_url( 'https://master-addons.com/pricing' ) . '" target="_blank"><span class="dashicons dashicons-star-filled" ' . $icon_style . '></span>Upgrade to Pro</a>',
        );
        $links = array_merge( $links, $new_links );
      }
      return $links;
    }

    /**
     * Remove Freemius action links from free plugin when Pro is active
     *
     * @param array $links Plugin action links
     * @return array Modified links
     */
    public function jltma_remove_freemius_action_links( $links ) {
      $freemius_keys = array(
        'upgrade',
        'pricing',
        'fs_upgrade',
        'opt-in',
        'opt-out',
        'change-license',
        'activate-license',
        'deactivate-license',
        'sync-license',
        'account',
        'contact',
        'support',
        'affiliation'
      );

      foreach ( $freemius_keys as $key ) {
        if ( isset( $links[ $key ] ) ) {
          unset( $links[ $key ] );
        }
      }
      return $links;
    }

    /**
     * Remove Freemius row meta links from free plugin when Pro is active
     *
     * @param array  $links Plugin row meta links
     * @param string $file  Plugin file
     * @return array Modified links
     */
    public function jltma_remove_freemius_row_meta( $links, $file ) {
      if ( $file === JLTMA_BASE ) {
        // Known Freemius row-meta keys. 'support' and 'documentation'
        // are included because we already ship our own branded versions
        // via jltma_plugin_row_meta and Freemius would otherwise add
        // plain-text duplicates next to ours.
        $freemius_keys = array(
          'upgrade',
          'pricing',
          'change-license',
          'opt-in',
          'opt-out',
          'activate-license',
          'deactivate-license',
          'support',
          'documentation',
          'docs',
        );

        foreach ( $freemius_keys as $key ) {
          if ( isset( $links[ $key ] ) ) {
            unset( $links[ $key ] );
          }
        }

        // Belt-and-suspenders: Freemius sometimes emits the links
        // without a stable array key. Scan by anchor text content and
        // drop any plain-text 'Support' / 'Documentation' entry that
        // isn't our own (ours contain the dashicons-* span class).
        foreach ( $links as $key => $html ) {
          if ( ! is_string( $html ) ) continue;
          if ( strpos( $html, 'dashicons-' ) !== false ) continue; // ours
          if ( preg_match( '~>(Support|Documentation)\s*<~i', $html ) ) {
            unset( $links[ $key ] );
          }
        }
      }
      return $links;
    }


    /**
     * Reorder admin submenu items
     * Places Freemius menus (Account, Support) at the end
     *
     * Desired order:
     * 1. Settings
     * 2. Template Library
     * 3. Theme Builder
     * 4. Account (Freemius)
     * 5. Support (Freemius)
     */
    public function jltma_reorder_admin_submenu() {
      global $submenu;

      $parent_slug = 'master-addons-settings';

      if ( !isset( $submenu[ $parent_slug ] ) ) {
        return;
      }

      // Define the desired order of menu slugs
      $desired_order = [
        'master-addons-settings',             // 0 - Settings
        'jltma-template-library',             // 1 - Template Library
        'jltma-template-kits',                // 2 - Template Kits
        'edit.php?post_type=master_template', // 3 - Theme Builder
        'edit.php?post_type=jltma_popup',     // 4 - Popup Builder
        'edit.php?post_type=jltma_widget',    // 5 - Widget Builder
        'master-addons-account',              // 6 - Account (Freemius)
        'master-addons-wp-support-forum',     // 7 - Support (Freemius)
      ];

      // Separate known items and unknown items
      $ordered_items = [];
      $remaining_items = [];

      foreach ( $submenu[ $parent_slug ] as $item ) {
        $slug = $item[2] ?? '';
        $position = array_search( $slug, $desired_order );

        if ( $position !== false ) {
          $ordered_items[ $position ] = $item;
        } else {
          // Unknown items go between Theme Builder and Account
          $remaining_items[] = $item;
        }
      }

      // Sort ordered items by their position
      ksort( $ordered_items );

      // Build final submenu array
      $final_submenu = [];

      // Add items in order, inserting remaining items after Widget Builder (position 5)
      foreach ( $ordered_items as $position => $item ) {
        $final_submenu[] = $item;

        // After Widget Builder, add remaining items (Recommended, Setup Wizard, etc.)
        if ( $position === 5 && !empty( $remaining_items ) ) {
          foreach ( $remaining_items as $remaining_item ) {
            $final_submenu[] = $remaining_item;
          }
          $remaining_items = []; // Clear to avoid duplicates
        }
      }

      // Add any remaining items at the end (if Theme Builder wasn't found)
      foreach ( $remaining_items as $remaining_item ) {
        $final_submenu[] = $remaining_item;
      }

      // Update the global submenu
      $submenu[ $parent_slug ] = $final_submenu;
    }


  }
}
Freemius_Hooks::get_instance();