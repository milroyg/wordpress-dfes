<?php

namespace MasterAddons\Modules\DynamicTags;

defined('ABSPATH') || exit;

// Don't load Dynamic Tags if Elementor Pro is active
// if (defined('ELEMENTOR_PRO_VERSION')) {
// 	return;
// }

if( !defined('JLTMA_DYNAMIC_TAGS_PATH_INC')){
  define('JLTMA_DYNAMIC_TAGS_PATH_INC', plugin_dir_path(__FILE__) . 'inc/');
}
if( !defined('JLTMA_DYNAMIC_TAGS_URL')){
  define('JLTMA_DYNAMIC_TAGS_URL', plugins_url('/', __FILE__));
}
if( !defined('JLTMA_DYNAMIC_TAGS_DIR')){
  define('JLTMA_DYNAMIC_TAGS_DIR', plugin_basename(__FILE__));
}


require plugin_dir_path(__FILE__) . 'class-dynamic-tags.php';
