<?php 
function theme_options_panel(){
  add_menu_page(
      'xevso Theme',
      'xevso Options',
      'manage_options', 
      'theme-options', 
      'xevso_options_settings', 
      'dashicons-podio',
        2
  );
  add_submenu_page( 'theme-options', 'Install Plugins', 'Install Plugins', 'manage_options', 'theme-plu-settings', 'xevso_plug_settings');
  add_submenu_page( 'theme-options', 'Import Demo', 'Import Demo', 'manage_options', 'demos-settings', 'xevso_demos_settings');
  
}
add_action('admin_menu', 'theme_options_panel');
 

function xevso_options_settings(){}
function xevso_regi_settings(){
        echo '<div class="wrap"><div id="icon-options-general" class="icon32"><br></div>
        <h2>Theme Registation</h2></div>';
}
function xevso_plug_settings(){
  $tgm_page_plugins = new TGM_Plugin_Activation();
  $tgm_page_plugins->install_plugins_page();
}
function xevso_demos_settings(){}