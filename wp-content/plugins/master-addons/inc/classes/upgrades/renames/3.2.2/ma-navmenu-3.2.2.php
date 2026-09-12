<?php

use MasterAddons\Inc\Classes\Upgrades\Control_Key_Migrator;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Navigation Menu control renames, as shipped in 3.2.2.
 *
 * One file per widget per release, named for both -- Control_Key_Migrator
 * reads the release from the folder and the widget from the file name, so a
 * later release's renames are a new folder rather than another screenful in
 * the class that applies them.
 *
 * Returns [ old control key => where its value goes ], where the destination
 * is one of three things:
 *
 *   - A control name, for a plain rename.
 *   - Control_Key_Migrator::DROPPED, for a control that is simply gone. The
 *     value is deleted rather than left behind for a future control to collide
 *     with.
 *   - An array, for anything that needs more than a move:
 *       'to'     the new control name,
 *       'group'  a key in Control_Key_Migrator::GROUP_FIELDS, when both names
 *                are group controls and every field they save has to travel,
 *       'sides'  the sides of a dimensions control a slider's one number
 *                becomes, for a pair of sliders replaced by one Padding.
 *                Merged rather than assigned, so the two halves of a pair land
 *                in the same value, and applied only to a value still in the
 *                old shape, so running it twice changes nothing,
 *       'values' old value => new value, for a control whose options were
 *                respelled,
 *       'with'   further keys to set when a value actually lands, which is how
 *                a lone colour becomes a visible border on the group control
 *                that replaced it. These are filled in after every move is
 *                done, so a real value migrated onto one of them always wins
 *                over the companion default.
 *
 * Controls that save nothing -- headings, dividers, the raw-HTML notices --
 * are not listed: there is no value under them to carry or to clear.
 *
 * Responsive siblings are covered automatically: a rename of `layout` carries
 * `layout_tablet`, `layout_mobile` and any other active breakpoint with it.
 *
 * @return array
 */

/**
 * A 1px solid border, as Group_Control_Border saves one.
 *
 * What a colour moved onto a border group has to be joined by, so a page that
 * only ever set the colour keeps the border the old control drew -- the old
 * Border Color controls only ever showed alongside a border the widget was
 * already drawing.
 */
$border_shown = array(
    'dropdown_toggle_border_border' => 'solid',
    'dropdown_toggle_border_width'  => array(
        'unit'     => 'px',
        'top'      => '1',
        'right'    => '1',
        'bottom'   => '1',
        'left'     => '1',
        'isLinked' => true,
    ),
);

/**
 * The same for a hover or active state.
 *
 * Style only -- a width here would override the one the normal state sets,
 * which is the width the border already had when only its colour changed on
 * hover -- and copied from the normal state rather than named, so a double
 * border does not turn solid under the pointer. A group writes its colour out
 * only while that same group has a style, which is why this is needed at all.
 */
$border_shown_hover = array(
    'dropdown_toggle_border_hover_border' => array('from' => 'dropdown_toggle_border_border'),
);

$border_shown_active = array(
    'dropdown_toggle_border_active_border' => array('from' => 'dropdown_toggle_border_border'),
);

return array(
    // ---- Content tab.
    'layout'             => 'jltma_nav_layout',
    'dropdown_menu_type' => 'jltma_dropdown_menu_type',
    // The Style tab has a `menu_alignment` of its own now -- what
    // is aligned inside an item, not where the row of them sits --
    // so this has to move off the name before that one reads it.
    'menu_alignment'     => 'jltma_nav_alignment',
    // One Alignment control now, for every dropdown type.
    'dropdown_popup_align' => 'dropdown_align',
    'dropdown_horizontal_distance' => 'jltma_dropdown_submenu_gap_left',

    // Gone with the layouts and options they belonged to.
    'dropdown_breakpoints'        => Control_Key_Migrator::DROPPED,
    'open_by_click'               => Control_Key_Migrator::DROPPED,
    'dropdown_absolute'           => Control_Key_Migrator::DROPPED,
    'dropdown_absolute_position'  => Control_Key_Migrator::DROPPED,
    'widget_min_width'            => Control_Key_Migrator::DROPPED,
    'dropdown_position'           => Control_Key_Migrator::DROPPED,
    'side_menu_alignment'         => Control_Key_Migrator::DROPPED,
    'indicator_submenu_popover'   => Control_Key_Migrator::DROPPED,
    // The submenu arrow is turned by degrees now, not by the name
    // of an animation, so there is no value to carry across.
    'indicator_submenu_animation' => Control_Key_Migrator::DROPPED,

    // Gone with the Dropdown Menu content section.
    'dropdown_popup_type_width'   => Control_Key_Migrator::DROPPED,
    'full_width'                  => Control_Key_Migrator::DROPPED,

    // ---- Main Menu Item. Two sliders became one Padding.
    'main_menu_item_horizontal_padding' => array(
        'to'    => 'main_menu_item_padding',
        'sides' => array('left', 'right'),
    ),
    'main_menu_item_vertical_padding' => array(
        'to'    => 'main_menu_item_padding',
        'sides' => array('top', 'bottom'),
    ),
    // The Hover and Active tabs' Border Color are fields of a
    // Group_Control_Border now, which composes its keys the other way round:
    // <group>_color, not <control>_<state>. (The normal tab's does too, and
    // lands on the name it already had, so it needs nothing here -- and
    // neither do Color and Background Color, which kept theirs.)
    //
    // A group's colour is only written out while that same group has a border
    // style, and the old state colours showed whenever the *normal* border had
    // one -- so the style is copied from there rather than guessed, which would
    // turn a double border solid under the pointer.
    'main_menu_item_border_color_hover' => array(
        'to'   => 'main_menu_item_border_hover_color',
        'with' => array(
            'main_menu_item_border_hover_border' => array('from' => 'main_menu_item_border_border'),
        ),
    ),
    'main_menu_item_border_color_active' => array(
        'to'   => 'main_menu_item_border_active_color',
        'with' => array(
            'main_menu_item_border_active_border' => array('from' => 'main_menu_item_border_border'),
        ),
    ),

    // ---- Side Box: the section is gone.
    'side_box_width' => Control_Key_Migrator::DROPPED,
    'side_box_bg'    => Control_Key_Migrator::DROPPED,

    // ---- Dropdown List.
    'dropdown_sublevel_bg'  => 'jltma_dropdown_submenu_bg_color',
    // The sublevel panel is spaced by its own padding now; there is
    // no margin control for this to become.
    'dropdown_sublevel_gap' => Control_Key_Migrator::DROPPED,

    // ---- Dropdown Item, main level. Its Border Color controls are
    // fields of a Group_Control_Border now -- the normal one lands
    // on the name it already had, the other two do not.
    'dropdown_main_level_border_color_hover' => array(
        'to'   => 'dropdown_main_level_border_hover_color',
        'with' => array(
            'dropdown_main_level_border_hover_border' => array('from' => 'dropdown_main_level_border_border'),
        ),
    ),
    'dropdown_main_level_border_color_active' => array(
        'to'   => 'dropdown_main_level_border_active_color',
        'with' => array(
            'dropdown_main_level_border_active_border' => array('from' => 'dropdown_main_level_border_border'),
        ),
    ),
    // Same name, new shape: a slider became a dimensions control.
    'dropdown_main_level_border_radius' => array(
        'to'    => 'dropdown_main_level_border_radius',
        'sides' => Control_Key_Migrator::ALL_SIDES,
    ),
    'dropdown_item_main_horizontal_padding' => array(
        'to'    => 'jltma_dropdown_menu_item_padding',
        'sides' => array('left', 'right'),
    ),
    'dropdown_item_main_vertical_padding' => array(
        'to'    => 'jltma_dropdown_menu_item_padding',
        'sides' => array('top', 'bottom'),
    ),

    // ---- Sublevel styles, now their own Submenu sections.
    'dropdown_typography' => array(
        'to'    => 'jltma_dropdown_submenu_typography',
        'group' => 'typography',
    ),
    'dropdown_item_border' => array(
        'to'    => 'jltma_dropdown_submenu_item_border',
        'group' => 'border',
    ),
    'dropdown_item_color'     => 'jltma_dropdown_submenu_item_color',
    'dropdown_item_bg_color'  => 'jltma_dropdown_submenu_item_bg',
    // The style and width travel with the group rename above, and the old
    // colour only ever showed alongside them, so this is a plain move.
    'dropdown_item_border_color' => 'jltma_dropdown_submenu_item_border_color',
    'dropdown_item_color_hover'    => 'jltma_dropdown_submenu_item_color_hover',
    'dropdown_item_bg_color_hover' => 'jltma_dropdown_submenu_item_bg_hover',
    'dropdown_item_border_color_hover' => array(
        'to'   => 'jltma_dropdown_submenu_item_border_hover_color',
        'with' => array(
            'jltma_dropdown_submenu_item_border_hover_border' => array('from' => 'jltma_dropdown_submenu_item_border_border'),
        ),
    ),
    'dropdown_item_color_active'    => 'jltma_dropdown_submenu_item_color_active',
    'dropdown_item_bg_color_active' => 'jltma_dropdown_submenu_item_bg_active',
    'dropdown_item_border_color_active' => array(
        'to'   => 'jltma_dropdown_submenu_item_border_active_color',
        'with' => array(
            'jltma_dropdown_submenu_item_border_active_border' => array('from' => 'jltma_dropdown_submenu_item_border_border'),
        ),
    ),
    'dropdown_item_horizontal_padding' => array(
        'to'    => 'jltma_dropdown_submenu_item_padding',
        'sides' => array('left', 'right'),
    ),
    'dropdown_item_vertical_padding' => array(
        'to'    => 'jltma_dropdown_submenu_item_padding',
        'sides' => array('top', 'bottom'),
    ),
    'dropdown_item_space_between'  => 'jltma_dropdown_submenu_item_gap',
    'dropdown_item_border_radius'  => 'jltma_dropdown_submenu_item_border_radius',
    // The submenu panel has a box shadow; its items no longer do.
    'dropdown_item_box_shadow' => array(
        'to'    => Control_Key_Migrator::DROPPED,
        'group' => 'box_shadow',
    ),

    // ---- Toggle Switch: three separate border controls became one
    // Group_Control_Border, saving under <name>_border, <name>_width
    // and <name>_color.
    'dropdown_toggle_framed_border_style' => array(
        'to'     => 'dropdown_toggle_border_border',
        // The old select's None was an empty string; the group
        // control spells it out.
        'values' => array('' => 'none'),
    ),
    'dropdown_toggle_framed_border_width' => 'dropdown_toggle_border_width',
    'dropdown_toggle_bd_color'            => array(
        'to'   => 'dropdown_toggle_border_color',
        // A colour on its own drew nothing until a style and a
        // width were set too; the old control's style defaulted to
        // one, so a page with only a colour set has to keep
        // showing a border.
        'with' => $border_shown,
    ),
    'dropdown_toggle_bd_color_hover'      => array(
        'to'   => 'dropdown_toggle_border_hover_color',
        'with' => $border_shown_hover,
    ),
    'dropdown_toggle_bd_color_active'     => array(
        'to'   => 'dropdown_toggle_border_active_color',
        'with' => $border_shown_active,
    ),
    // The circle toggle takes the same Padding as the square one.
    'dropdown_toggle_icon_padding' => Control_Key_Migrator::DROPPED,

    // ---- Side Box border: the section is gone.
    'side_box_border' => array(
        'to'    => Control_Key_Migrator::DROPPED,
        'group' => 'border',
    ),
);
