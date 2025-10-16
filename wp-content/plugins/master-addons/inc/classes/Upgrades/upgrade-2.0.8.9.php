<?php
namespace MasterAddons\Inc\Classes;

function jltma_update_icon_libraries_settings_key()
{
    $jltma_settings_key = get_option('jltma_icons_library_save_settings', '');

    if (!empty($jltma_settings_key)) {
        if (!empty($jltma_settings_key['admin_general_button_color'])) {
            $button_colors = $jltma_settings_key['admin_general_button_color'];

            $new_button_colors = [
                'primary_color'   => '#0347FF',
                'secondary_color' => '#fff',
            ];

            if (is_array($button_colors) && array_key_exists('bg_color', $button_colors)) {
                $new_button_colors['primary_color'] = esc_attr($button_colors['bg_color']);
            }
            if (is_array($button_colors) && array_key_exists('text_color', $button_colors)) {
                $new_button_colors['secondary_color'] = esc_attr($button_colors['text_color']);
            }

            $jltma_settings_key['admin_general_button_color'] = $new_button_colors;

            update_option('jltma_icons_library_save_settings', $jltma_settings_key);
        }
    }
}
jltma_update_icon_libraries_settings_key();


// update version once migration is completed.
update_option($this->option_name, $version);
