<?php
namespace MasterAddons\Admin\WidgetBuilder\Controls;

defined('ABSPATH') || exit;

/**
 * DATE_TIME Control
 * Handles Elementor DATE_TIME control type
 * Date and time picker control (uses Flatpickr library)
 *
 * Supported properties:
 * - label: Control label
 * - name: Field name (used for control key)
 * - description: Description below field
 * - default: Default date/time value
 * - picker_options: Options for the date/time picker (array)
 *   - enableTime: Enable time picker (boolean)
 *   - noCalendar: Hide calendar, show only time picker (boolean)
 *   - dateFormat: Date format string (e.g., 'Y-m-d H:i')
 *   - time_24hr: Use 24-hour time format (boolean)
 *   - minuteIncrement: Minute step value (integer, default: 1)
 *   - defaultDate: Default date/time (string)
 *   - minDate: Minimum selectable date (string)
 *   - maxDate: Maximum selectable date (string)
 *
 * Common settings (handled by base class):
 * - show_label: Show/hide label
 * - label_block: Display label on separate line
 * - dynamic: Enable dynamic content support
 * - separator: Control separator position
 * - condition: Conditional display logic
 */
class Date_Time extends Control_Base {

    public function get_type() {
        return 'DATE_TIME';
    }

    public function build($control_key, $field) {
        $label = !empty($field['label']) ? $field['label'] : 'Date Time';

        // Check if responsive control
        $is_responsive = isset($field['responsive']) && $field['responsive'];

        // Build control header
        $content = $this->build_control_header($control_key, $label, 'DATE_TIME', $is_responsive);

        // Add DATE_TIME-specific properties FIRST
        $content .= $this->build_date_time_properties($field);

        // Add common properties
        $content .= $this->build_common_properties($field);

        // Close control
        $content .= $this->build_control_footer();

        return $content;
    }

    /**
     * Build DATE_TIME-specific properties
     *
     * @param array $field Field configuration
     * @return string
     */
    protected function build_date_time_properties($field) {
        $content = '';

        // Picker options
        if (!empty($field['picker_options']) && is_array($field['picker_options'])) {
            $content .= "\t\t\t\t'picker_options' => [\n";

            $picker_opts = $field['picker_options'];

            // enableTime - Enable time picker
            if (isset($picker_opts['enableTime'])) {
                $content .= "\t\t\t\t\t'enableTime' => " . ($picker_opts['enableTime'] ? 'true' : 'false') . ",\n";
            }

            // noCalendar - Show only time picker (hide calendar)
            if (isset($picker_opts['noCalendar'])) {
                $content .= "\t\t\t\t\t'noCalendar' => " . ($picker_opts['noCalendar'] ? 'true' : 'false') . ",\n";
            }

            // dateFormat - Date/time format string
            if (!empty($picker_opts['dateFormat'])) {
                $content .= "\t\t\t\t\t'dateFormat' => '" . esc_js($picker_opts['dateFormat']) . "',\n";
            }

            // time_24hr - Use 24-hour format
            if (isset($picker_opts['time_24hr'])) {
                $content .= "\t\t\t\t\t'time_24hr' => " . ($picker_opts['time_24hr'] ? 'true' : 'false') . ",\n";
            }

            // minuteIncrement - Minute step value
            if (isset($picker_opts['minuteIncrement']) && is_numeric($picker_opts['minuteIncrement'])) {
                $content .= "\t\t\t\t\t'minuteIncrement' => " . intval($picker_opts['minuteIncrement']) . ",\n";
            }

            // defaultDate - Default date/time
            if (!empty($picker_opts['defaultDate'])) {
                $content .= "\t\t\t\t\t'defaultDate' => '" . esc_js($picker_opts['defaultDate']) . "',\n";
            }

            // minDate - Minimum selectable date
            if (!empty($picker_opts['minDate'])) {
                $content .= "\t\t\t\t\t'minDate' => '" . esc_js($picker_opts['minDate']) . "',\n";
            }

            // maxDate - Maximum selectable date
            if (!empty($picker_opts['maxDate'])) {
                $content .= "\t\t\t\t\t'maxDate' => '" . esc_js($picker_opts['maxDate']) . "',\n";
            }

            $content .= "\t\t\t\t],\n";
        }

        return $content;
    }
}
