<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

use MasterAddons\Inc\Helper\Master_Addons_Helper;

/**
 * Template Kits System Initialization
 *
 * This replaces the old jltma-templates-kit.php file with a modular approach
 */

// Include Template Kits modules
if(Master_Addons_Helper::jltma_premium() && defined('JLTMA_PRO_PATH')){
    require_once JLTMA_PRO_PATH . 'inc/templates/kits/template-kits.php';
    require_once JLTMA_PRO_PATH . 'inc/templates/kits/ajax-handlers.php';
    require_once JLTMA_PRO_PATH . 'inc/templates/kits/importer.php';
}else{
    require_once JLTMA_PATH . 'inc/templates/kits/template-kits.php';
    require_once JLTMA_PATH . 'inc/templates/kits/ajax-handlers.php';
    require_once JLTMA_PATH . 'inc/templates/kits/importer.php';
}
