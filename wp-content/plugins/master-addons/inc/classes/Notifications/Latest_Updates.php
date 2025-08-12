<?php

namespace MasterAddons\Inc\Classes\Notifications;

use MasterAddons\Inc\Classes\Notifications\Model\Notice;

if (!class_exists('Latest_Updates')) {
    /**
     * Latest Pugin Updates Notice Class
     *
     * Jewel Theme <support@jeweltheme.com>
     */
    class Latest_Updates extends Notice
    {

        /**
         * Latest Updates Notice
         *
         * @return void
         */
        public function __construct()
        {
            parent::__construct();
        }


        /**
         * Notice Content
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public function notice_content()
        {
            $jltma_changelog_message = sprintf(
                __('%3$s %4$s %5$s %6$s %7$s %8$s <br> <strong>Check Changelogs for </strong> <a href="%1$s" target="__blank">%2$s</a>', 'master-addons'),
                esc_url_raw('https://master-addons.com/changelogs'),
                __('More Details', 'master-addons'),
                /** Changelog Items
                 * Starts from: %3$s
                 */

                '<h3 class="jltma-update-head">' . JLTMA . ' <span><small><em>v' . esc_html(JLTMA_VER) . '</em></small>' . __(' has some updates..', 'master-addons') . '</span></h3><br>', // %3$s
                __('<span class="dashicons dashicons-yes"></span> <span class="jltma-changes-list"> Mega Menu - Data Saving, Editor Close button position updated </span><br>', 'master-addons'),
                __('<span class="dashicons dashicons-yes"></span> <span class="jltma-changes-list"> Security Update & Performance issue fixed </span><br>', 'master-addons'),
                __('<span class="dashicons dashicons-yes"></span> <span class="jltma-changes-list"> Plugins iframe popup thickbox style issue updated </span><br>', 'master-addons'),
                __('<span class="dashicons dashicons-yes"></span> <span class="jltma-changes-list"> MA Templates save issue fixed </span><br>', 'master-addons'),
                __('<span class="dashicons dashicons-yes"></span> <span class="jltma-changes-list"> Global Typography notice issue fixed </span><br>', 'master-addons')
            );

            printf(wp_kses_post($jltma_changelog_message));
        }

        /**
         * Intervals
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public function intervals()
        {
            return array(0);
        }
    }
}
