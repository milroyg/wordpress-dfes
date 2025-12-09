<?php

namespace MasterAddons\Modules\DynamicTags;

defined('ABSPATH') || exit;

// Don't load Dynamic Tags if Elementor Pro is active
// if (defined('ELEMENTOR_PRO_VERSION')) {
// 	return;
// }

define('JLTMA_DYNAMIC_TAGS_PATH_INC', plugin_dir_path(__FILE__) . 'inc/');
define('JLTMA_DYNAMIC_TAGS_URL', plugins_url('/', __FILE__));
define('JLTMA_DYNAMIC_TAGS_DIR', plugin_basename(__FILE__));

require plugin_dir_path(__FILE__) . 'class-dynamic-tags.php';
