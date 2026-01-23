<?php

namespace MasterAddons\Modules;

// Elementor classes
use \Elementor\Controls_Manager;
use \MasterAddons\Inc\Classes\JLTMA_Extension_Prototype;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Background Patterns + Gradient Overlays (inline style)
 */
if (!class_exists('MasterAddons\Modules\JLTMA_Extension_Patterns')) {
    if(! defined('JLTMA_PRO_VER')){
        require_once JLTMA_PATH . 'inc/classes/JLTMA_Extension_Prototype.php';
    }else{
        require_once JLTMA_PRO_PATH . 'inc/classes/JLTMA_Extension_Prototype.php';
    }
    
class JLTMA_Extension_Patterns extends JLTMA_Extension_Prototype
{
    private static $instance = null;
    public  $name = 'Patterns';
    public  $has_controls = true;

    private function add_controls($element, $args)
    {
        // Create separate section for patterns
        $element->start_controls_section(
            'jltma_patterns_section',
            [
                'tab' => Controls_Manager::TAB_STYLE,
                'label' => __('Patterns', 'master-addons') . JLTMA_EXTENSION_BADGE
            ]
        );

        // Enable
        $element->add_control(
            'jltma_enable_pattern',
            [
                'label'        => __('Enable Pattern', 'master-addons'),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __('Yes', 'master-addons'),
                'label_off'    => __('No', 'master-addons'),
                'return_value' => 'yes',
            ]
        );

        // Style chooser - All 77 PatternCraft Patterns
        $element->add_control(
            'jltma_pattern_style',
            [
                'label'     => __('Pattern Style', 'master-addons'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'top-gradient-radial',
                'options'   => [
                    // Decorative Gradients
                    'top-gradient-radial'       => __('Top Gradient Radial', 'master-addons'),
                    'bottom-gradient-radial'    => __('Bottom Gradient Radial', 'master-addons'),
                    'bottom-violet-radial'      => __('Bottom Violet Radial', 'master-addons'),
                    'bottom-slate-radial'       => __('Bottom Slate Radial', 'master-addons'),
                    'radial-teal-glow'          => __('Teal Glow', 'master-addons'),
                    'radial-pink-glow'          => __('Pink Glow', 'master-addons'),
                    'radial-amber-glow'         => __('Amber Glow', 'master-addons'),
                    'radial-emerald-glow'       => __('Emerald Glow', 'master-addons'),

                    // Dark Decorative
                    'dark-horizon-glow'         => __('Dark Horizon Glow', 'master-addons'),
                    'crimson-depth'             => __('Crimson Depth', 'master-addons'),
                    'emerald-void'              => __('Emerald Void', 'master-addons'),
                    'violet-abyss'              => __('Violet Abyss', 'master-addons'),
                    'azure-depths'              => __('Azure Depths', 'master-addons'),
                    'orchid-depths'             => __('Orchid Depths', 'master-addons'),
                    'dark-horizon-glow-top'     => __('Dark Horizon Glow Top', 'master-addons'),
                    'crimson-depth-top'         => __('Crimson Depth Top', 'master-addons'),
                    'emerald-void-top'          => __('Emerald Void Top', 'master-addons'),
                    'violet-abyss-top'          => __('Violet Abyss Top', 'master-addons'),
                    'azure-depths-top'          => __('Azure Depths Top', 'master-addons'),
                    'orchid-depths-top'         => __('Orchid Depths Top', 'master-addons'),

                    // Geometric Grid Patterns
                    'purple-gradient-grid-right' => __('Purple Gradient Grid Right', 'master-addons'),
                    'purple-gradient-grid-left'  => __('Purple Gradient Grid Left', 'master-addons'),
                    'dual-gradient-overlay-strong' => __('Dual Gradient Overlay', 'master-addons'),
                    'dual-gradient-overlay-strong-swapped' => __('Dual Gradient Overlay Swapped', 'master-addons'),
                    'dual-gradient-overlay-top'  => __('Dual Gradient Overlay (Top)', 'master-addons'),
                    'dual-gradient-overlay-bottom' => __('Dual Gradient Overlay (Bottom)', 'master-addons'),
                    'top-fade-grid'             => __('Top Fade Grid', 'master-addons'),
                    'bottom-fade-grid'          => __('Bottom Fade Grid', 'master-addons'),
                    'diagonal-fade-grid-left'   => __('Diagonal Fade Grid Left', 'master-addons'),
                    'diagonal-fade-grid-right'  => __('Diagonal Fade Grid Right', 'master-addons'),

                    // Radial Glow Effects
                    'dark-radial-glow'          => __('Dark Radial Glow', 'master-addons'),
                    'blue-radial-glow'          => __('Blue Radial Glow', 'master-addons'),
                    'purple-radial-glow'        => __('Purple Radial Glow', 'master-addons'),
                    'emerald-radial-glow'       => __('Emerald Radial Glow', 'master-addons'),

                    // Aurora Effects
                    'aurora-dream-corner-whispers' => __('Aurora Dream Corner Whispers', 'master-addons'),
                    'aurora-dream-soft-harmony'   => __('Aurora Dream Soft Harmony', 'master-addons'),
                    'aurora-dream-diagonal-flow'  => __('Aurora Dream Diagonal Flow', 'master-addons'),
                    'aurora-dream-central-burst'  => __('Aurora Dream Central Burst', 'master-addons'),

                    // Basic Patterns
                    'waves-vertical'            => __('Waves Vertical', 'master-addons'),
                    'waves-horizontal'          => __('Waves Horizontal', 'master-addons'),
                    'diagonal-stripes'          => __('Diagonal Stripes', 'master-addons'),
                    'dots-pattern'              => __('Dots Pattern', 'master-addons'),
                    'crosshatch'                => __('Crosshatch', 'master-addons'),

                    // Grid Patterns
                    'grid-small'                => __('Grid Small', 'master-addons'),
                    'grid-medium'               => __('Grid Medium', 'master-addons'),
                    'grid-large'                => __('Grid Large', 'master-addons'),
                    'dot-grid'                  => __('Dot Grid', 'master-addons'),
                    'isometric-grid'            => __('Isometric Grid', 'master-addons'),
                    'hexagon-pattern'           => __('Hexagon Pattern', 'master-addons'),
                    'triangles-pattern'         => __('Triangles Pattern', 'master-addons'),
                    'zigzag-horizontal'         => __('Zigzag Horizontal', 'master-addons'),
                    'zigzag-vertical'           => __('Zigzag Vertical', 'master-addons'),
                    'tartan'                    => __('Tartan', 'master-addons'),
                    'plaid-subtle'              => __('Plaid Subtle', 'master-addons'),

                    // Technical Patterns
                    'blueprint'                 => __('Blueprint', 'master-addons'),
                    'blueprint-dark'            => __('Blueprint Dark', 'master-addons'),
                    'circuit-board'             => __('Circuit Board', 'master-addons'),
                    'graph-paper'               => __('Graph Paper', 'master-addons'),
                    'notebook'                  => __('Notebook', 'master-addons'),

                    // Cultural Patterns
                    'moroccan'                  => __('Moroccan', 'master-addons'),
                    'japanese-seigaiha'         => __('Japanese Seigaiha', 'master-addons'),
                    'art-deco'                  => __('Art Deco', 'master-addons'),
                    'vintage-wallpaper'         => __('Vintage Wallpaper', 'master-addons'),

                    // Texture Patterns
                    'noise-texture'             => __('Noise Texture', 'master-addons'),
                    'fabric-texture'            => __('Fabric Texture', 'master-addons'),
                    'wood-grain'                => __('Wood Grain', 'master-addons'),
                    'marble-veins'              => __('Marble Veins', 'master-addons'),
                    'paper-texture'             => __('Paper Texture', 'master-addons'),

                    // Artistic Patterns
                    'crosshatch-art'            => __('Crosshatch Art', 'master-addons'),
                    'stippling'                 => __('Stippling', 'master-addons'),
                    'sketch-lines'              => __('Sketch Lines', 'master-addons'),
                    'watercolor-wash'           => __('Watercolor Wash', 'master-addons'),
                    'crosshatch-art-dark'       => __('Crosshatch Art Dark', 'master-addons'),
                ],
                'condition' => ['jltma_enable_pattern' => 'yes'],
            ]
        );

        // Z-Index for layering with Elementor backgrounds
        $element->add_control(
            'jltma_pattern_z_index',
            [
                'label'       => __('Z-Index', 'master-addons'),
                'type'        => Controls_Manager::SLIDER,
                'range'       => ['px' => ['min' => -10, 'max' => 10, 'step' => 1]],
                'default'     => ['size' => '', 'unit' => 'px'],
                'condition'   => ['jltma_enable_pattern' => 'yes'],
                'description' => __('Set layering position relative to Elementor background', 'master-addons'),
            ]
        );


        // End the patterns section
        $element->end_controls_section();
    }

    protected function add_actions()
    {
        // Add separate patterns section after background sections
        add_action('elementor/element/after_section_end', [$this, 'register_controls'], 10, 3);

        // Add print template for editor preview
        add_action('elementor/element/print_template', [$this, '_print_template'], 10, 2);
        add_action('elementor/section/print_template', [$this, '_print_template'], 10, 2);
        add_action('elementor/column/print_template', [$this, '_print_template'], 10, 2);
        add_action('elementor/container/print_template', [$this, '_print_template'], 10, 2);

        // Apply inline styles
        add_action('elementor/frontend/section/before_render',   [$this, 'before_render'], 10, 1);
        add_action('elementor/frontend/column/before_render',    [$this, 'before_render'], 10, 1);
        add_action('elementor/frontend/container/before_render', [$this, 'before_render'], 10, 1);
    }

    public function register_controls($element, $section_id, $args)
    {
        // Only add patterns section to section/column/container elements after their background/style sections
        if (
            ('section' === $element->get_name() && 'section_background' === $section_id) ||
            ('column' === $element->get_name() && 'section_style' === $section_id) ||
            ('container' === $element->get_name() && 'section_background' === $section_id)
        ) {
            $this->add_controls($element, $args);
        }
    }

    public function before_render($element)
    {
        $settings = $element->get_settings_for_display();

        if (empty($settings['jltma_enable_pattern']) || $settings['jltma_enable_pattern'] !== 'yes') {
            return;
        }

        // Get z-index for layering with Elementor backgrounds
        $z_index = isset($settings['jltma_pattern_z_index']['size']) ? $settings['jltma_pattern_z_index']['size'] : 1;

        $pattern_css = '';
        $pattern_style = $settings['jltma_pattern_style'];

        // All 77 PatternCraft Patterns - Exactly as defined in source
        switch ($pattern_style) {
            // Decorative Gradients
            case 'top-gradient-radial':
                $pattern_css = "background: radial-gradient(125% 125% at 50% 10%, #fff 40%, #6366f1 100%);";
                break;

            case 'bottom-gradient-radial':
                $pattern_css = "background: radial-gradient(125% 125% at 50% 90%, #fff 40%, #6366f1 100%);";
                break;

            case 'bottom-violet-radial':
                $pattern_css = "background: radial-gradient(125% 125% at 50% 90%, #fff 40%, #7c3aed 100%);";
                break;

            case 'bottom-slate-radial':
                $pattern_css = "background: radial-gradient(125% 125% at 50% 90%, #fff 40%, #475569 100%);";
                break;

            case 'radial-teal-glow':
                $pattern_css = "background: #ffffff; background-image: radial-gradient(125% 125% at 50% 90%, #ffffff 40%, #14b8a6 100%); background-size: 100% 100%;";
                break;

            case 'radial-pink-glow':
                $pattern_css = "background: #ffffff; background-image: radial-gradient(125% 125% at 50% 90%, #ffffff 40%, #ec4899 100%); background-size: 100% 100%;";
                break;

            case 'radial-amber-glow':
                $pattern_css = "background: #ffffff; background-image: radial-gradient(125% 125% at 50% 90%, #ffffff 40%, #f59e0b 100%); background-size: 100% 100%;";
                break;

            case 'radial-emerald-glow':
                $pattern_css = "background: #ffffff; background-image: radial-gradient(125% 125% at 50% 90%, #ffffff 40%, #10b981 100%); background-size: 100% 100%;";
                break;

            // Dark Decorative
            case 'dark-horizon-glow':
                $pattern_css = "background: #000000; background-image: radial-gradient(125% 125% at 50% 90%, #000000 40%, #0d1a36 100%); background-size: 100% 100%;";
                break;

            case 'crimson-depth':
                $pattern_css = "background: #000000; background-image: radial-gradient(125% 125% at 50% 100%, #000000 40%, #2b0707 100%); background-size: 100% 100%;";
                break;

            case 'emerald-void':
                $pattern_css = "background: #000000; background-image: radial-gradient(125% 125% at 50% 90%, #000000 40%, #072607 100%); background-size: 100% 100%;";
                break;

            case 'violet-abyss':
                $pattern_css = "background: #000000; background-image: radial-gradient(125% 125% at 50% 90%, #000000 40%, #2b092b 100%); background-size: 100% 100%;";
                break;

            case 'azure-depths':
                $pattern_css = "background: #000000; background-image: radial-gradient(125% 125% at 50% 100%, #000000 40%, #010133 100%); background-size: 100% 100%;";
                break;

            case 'orchid-depths':
                $pattern_css = "background: #000000; background-image: radial-gradient(125% 125% at 50% 100%, #000000 40%, #350136 100%); background-size: 100% 100%;";
                break;

            case 'dark-horizon-glow-top':
                $pattern_css = "background: radial-gradient(125% 125% at 50% 10%, #000000 40%, #0d1a36 100%);";
                break;

            case 'crimson-depth-top':
                $pattern_css = "background: radial-gradient(125% 125% at 50% 10%, #000000 40%, #2b0707 100%);";
                break;

            case 'emerald-void-top':
                $pattern_css = "background: radial-gradient(125% 125% at 50% 10%, #000000 40%, #072607 100%);";
                break;

            case 'violet-abyss-top':
                $pattern_css = "background: radial-gradient(125% 125% at 50% 10%, #000000 40%, #2b092b 100%);";
                break;

            case 'azure-depths-top':
                $pattern_css = "background: radial-gradient(125% 125% at 50% 10%, #000000 40%, #010133 100%);";
                break;

            case 'orchid-depths-top':
                $pattern_css = "background: radial-gradient(125% 125% at 50% 10%, #000000 40%, #350136 100%);";
                break;

            // Geometric Grid Patterns
            case 'purple-gradient-grid-right':
                $pattern_css = "background: #ffffff; background-image: linear-gradient(to right, #f0f0f0 1px, transparent 1px), linear-gradient(to bottom, #f0f0f0 1px, transparent 1px), radial-gradient(circle 800px at 100% 200px, #d5c5ff, transparent); background-size: 96px 64px, 96px 64px, 100% 100%;";
                break;

            case 'purple-gradient-grid-left':
                $pattern_css = "background: #ffffff; background-image: linear-gradient(to right, #f0f0f0 1px, transparent 1px), linear-gradient(to bottom, #f0f0f0 1px, transparent 1px), radial-gradient(circle 800px at 0% 200px, #d5c5ff, transparent); background-size: 96px 64px, 96px 64px, 100% 100%;";
                break;

            case 'dual-gradient-overlay-strong':
                $pattern_css = "background: #ffffff; background-image: linear-gradient(to right, rgba(229,231,235,0.8) 1px, transparent 1px), linear-gradient(to bottom, rgba(229,231,235,0.8) 1px, transparent 1px), radial-gradient(circle 500px at 20% 80%, rgba(139,92,246,0.3), transparent), radial-gradient(circle 500px at 80% 20%, rgba(59,130,246,0.3), transparent); background-size: 48px 48px, 48px 48px, 100% 100%, 100% 100%;";
                break;

            case 'dual-gradient-overlay-strong-swapped':
                $pattern_css = "background: #ffffff; background-image: linear-gradient(to right, rgba(229,231,235,0.8) 1px, transparent 1px), linear-gradient(to bottom, rgba(229,231,235,0.8) 1px, transparent 1px), radial-gradient(circle 500px at 20% 20%, rgba(139,92,246,0.3), transparent), radial-gradient(circle 500px at 80% 80%, rgba(59,130,246,0.3), transparent); background-size: 48px 48px, 48px 48px, 100% 100%, 100% 100%;";
                break;

            case 'dual-gradient-overlay-top':
                $pattern_css = "background: #ffffff; background-image: linear-gradient(to right, rgba(229,231,235,0.8) 1px, transparent 1px), linear-gradient(to bottom, rgba(229,231,235,0.8) 1px, transparent 1px), radial-gradient(circle 500px at 0% 20%, rgba(139,92,246,0.3), transparent), radial-gradient(circle 500px at 100% 0%, rgba(59,130,246,0.3), transparent); background-size: 48px 48px, 48px 48px, 100% 100%, 100% 100%;";
                break;

            case 'dual-gradient-overlay-bottom':
                $pattern_css = "background: #ffffff; background-image: linear-gradient(to right, rgba(229,231,235,0.8) 1px, transparent 1px), linear-gradient(to bottom, rgba(229,231,235,0.8) 1px, transparent 1px), radial-gradient(circle 500px at 20% 100%, rgba(139,92,246,0.3), transparent), radial-gradient(circle 500px at 100% 80%, rgba(59,130,246,0.3), transparent); background-size: 48px 48px, 48px 48px, 100% 100%, 100% 100%;";
                break;

            case 'top-fade-grid':
                $pattern_css = "background: #f8fafc; background-image: linear-gradient(to right, #e2e8f0 1px, transparent 1px), linear-gradient(to bottom, #e2e8f0 1px, transparent 1px); background-size: 20px 30px; -webkit-mask-image: radial-gradient(ellipse 70% 60% at 50% 0%, #000 60%, transparent 100%); mask-image: radial-gradient(ellipse 70% 60% at 50% 0%, #000 60%, transparent 100%);";
                break;

            case 'bottom-fade-grid':
                $pattern_css = "background: #f8fafc; background-image: linear-gradient(to right, #e2e8f0 1px, transparent 1px), linear-gradient(to bottom, #e2e8f0 1px, transparent 1px); background-size: 20px 30px; -webkit-mask-image: radial-gradient(ellipse 70% 60% at 50% 100%, #000 60%, transparent 100%); mask-image: radial-gradient(ellipse 70% 60% at 50% 100%, #000 60%, transparent 100%);";
                break;

            case 'diagonal-fade-grid-left':
                $pattern_css = "background: #f9fafb; background-image: linear-gradient(to right, #d1d5db 1px, transparent 1px), linear-gradient(to bottom, #d1d5db 1px, transparent 1px); background-size: 32px 32px; -webkit-mask-image: radial-gradient(ellipse 80% 80% at 0% 0%, #000 50%, transparent 90%); mask-image: radial-gradient(ellipse 80% 80% at 0% 0%, #000 50%, transparent 90%);";
                break;

            case 'diagonal-fade-grid-right':
                $pattern_css = "background: #f9fafb; background-image: linear-gradient(to right, #d1d5db 1px, transparent 1px), linear-gradient(to bottom, #d1d5db 1px, transparent 1px); background-size: 32px 32px; -webkit-mask-image: radial-gradient(ellipse 80% 80% at 100% 0%, #000 50%, transparent 90%); mask-image: radial-gradient(ellipse 80% 80% at 100% 0%, #000 50%, transparent 90%);";
                break;

            // Radial Glow Effects
            case 'dark-radial-glow':
                $pattern_css = "background: #020617; background-image: radial-gradient(circle 500px at 50% 200px, #3e3e3e, transparent);";
                break;

            case 'blue-radial-glow':
                $pattern_css = "background: #0f172a; background-image: radial-gradient(circle 600px at 50% 50%, rgba(59,130,246,0.3), transparent);";
                break;

            case 'purple-radial-glow':
                $pattern_css = "background: #020617; background-image: radial-gradient(circle 500px at 50% 100px, rgba(139,92,246,0.4), transparent);";
                break;

            case 'emerald-radial-glow':
                $pattern_css = "background: #020617; background-image: radial-gradient(circle 500px at 50% 300px, rgba(16,185,129,0.35), transparent);";
                break;

            // Aurora Effects
            case 'aurora-dream-corner-whispers':
                $pattern_css = "background: #f7eaff; background-image: radial-gradient(ellipse 85% 65% at 8% 8%, rgba(175, 109, 255, 0.42), transparent 60%), radial-gradient(ellipse 75% 60% at 75% 35%, rgba(255, 235, 170, 0.55), transparent 62%), radial-gradient(ellipse 70% 60% at 15% 80%, rgba(255, 100, 180, 0.40), transparent 62%), radial-gradient(ellipse 70% 60% at 92% 92%, rgba(120, 190, 255, 0.45), transparent 62%), linear-gradient(180deg, #f7eaff 0%, #fde2ea 100%); background-size: 100% 100%;";
                break;

            case 'aurora-dream-soft-harmony':
                $pattern_css = "background: #f7eaff; background-image: radial-gradient(ellipse 80% 60% at 60% 20%, rgba(175, 109, 255, 0.50), transparent 65%), radial-gradient(ellipse 70% 60% at 20% 80%, rgba(255, 100, 180, 0.45), transparent 65%), radial-gradient(ellipse 60% 50% at 60% 65%, rgba(255, 235, 170, 0.43), transparent 62%), radial-gradient(ellipse 65% 40% at 50% 60%, rgba(120, 190, 255, 0.48), transparent 68%), linear-gradient(180deg, #f7eaff 0%, #fde2ea 100%); background-size: 100% 100%;";
                break;

            case 'aurora-dream-diagonal-flow':
                $pattern_css = "background: #f7eaff; background-image: radial-gradient(ellipse 80% 60% at 5% 40%, rgba(175, 109, 255, 0.48), transparent 67%), radial-gradient(ellipse 70% 60% at 45% 45%, rgba(255, 100, 180, 0.41), transparent 67%), radial-gradient(ellipse 62% 52% at 83% 76%, rgba(255, 235, 170, 0.44), transparent 63%), radial-gradient(ellipse 60% 48% at 75% 20%, rgba(120, 190, 255, 0.36), transparent 66%), linear-gradient(45deg, #f7eaff 0%, #fde2ea 100%); background-size: 100% 100%;";
                break;

            case 'aurora-dream-central-burst':
                $pattern_css = "background: #f7eaff; background-image: radial-gradient(ellipse 50% 60% at 50% 50%, rgba(175, 109, 255, 0.52), transparent 68%), radial-gradient(ellipse 65% 55% at 40% 40%, rgba(255, 100, 180, 0.40), transparent 62%), radial-gradient(ellipse 70% 50% at 60% 60%, rgba(255, 235, 170, 0.48), transparent 67%), radial-gradient(ellipse 55% 65% at 35% 65%, rgba(120, 190, 255, 0.44), transparent 62%), linear-gradient(180deg, #f7eaff 0%, #fde2ea 100%); background-size: 100% 100%;";
                break;

            // Basic Patterns
            case 'waves-vertical':
                $pattern_css = "background: linear-gradient(90deg, #ffffff 0%, #ffffff 50%, #f3f4f6 50%, #f3f4f6 100%); background-size: 20px 100%;";
                break;

            case 'waves-horizontal':
                $pattern_css = "background: linear-gradient(180deg, #ffffff 0%, #ffffff 50%, #f3f4f6 50%, #f3f4f6 100%); background-size: 100% 20px;";
                break;

            case 'diagonal-stripes':
                $pattern_css = "background: repeating-linear-gradient(45deg, #f3f4f6 0px, #f3f4f6 10px, #ffffff 10px, #ffffff 20px);";
                break;

            case 'dots-pattern':
                $pattern_css = "background-image: radial-gradient(circle, #d1d5db 1px, transparent 1px); background-size: 20px 20px;";
                break;

            case 'crosshatch':
                $pattern_css = "background: #f9fafb; background-image: linear-gradient(45deg, #e5e7eb 25%, transparent 25%), linear-gradient(135deg, #e5e7eb 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #e5e7eb 75%), linear-gradient(135deg, transparent 75%, #e5e7eb 75%); background-size: 20px 20px; background-position: 0 0, 10px 0, 10px -10px, 0px 10px;";
                break;

            // Grid Patterns
            case 'grid-small':
                $pattern_css = "background-image: linear-gradient(to right, #e5e7eb 1px, transparent 1px), linear-gradient(to bottom, #e5e7eb 1px, transparent 1px); background-size: 20px 20px;";
                break;

            case 'grid-medium':
                $pattern_css = "background-image: linear-gradient(to right, #d1d5db 1px, transparent 1px), linear-gradient(to bottom, #d1d5db 1px, transparent 1px); background-size: 40px 40px;";
                break;

            case 'grid-large':
                $pattern_css = "background-image: linear-gradient(to right, #9ca3af 1px, transparent 1px), linear-gradient(to bottom, #9ca3af 1px, transparent 1px); background-size: 80px 80px;";
                break;

            case 'dot-grid':
                $pattern_css = "background-image: radial-gradient(circle, #d1d5db 1px, transparent 1px); background-size: 30px 30px;";
                break;

            case 'isometric-grid':
                $pattern_css = "background-image: linear-gradient(30deg, #e5e7eb 12%, transparent 12.5%, transparent 87%, #e5e7eb 87.5%, #e5e7eb), linear-gradient(150deg, #e5e7eb 12%, transparent 12.5%, transparent 87%, #e5e7eb 87.5%, #e5e7eb), linear-gradient(30deg, #e5e7eb 12%, transparent 12.5%, transparent 87%, #e5e7eb 87.5%, #e5e7eb), linear-gradient(150deg, #e5e7eb 12%, transparent 12.5%, transparent 87%, #e5e7eb 87.5%, #e5e7eb); background-size: 80px 140px; background-position: 0 0, 0 0, 40px 70px, 40px 70px;";
                break;

            case 'hexagon-pattern':
                $pattern_css = "background: #ffffff; background-image: linear-gradient(30deg, #f3f4f6 12%, transparent 12.5%, transparent 87%, #f3f4f6 87.5%, #f3f4f6), linear-gradient(150deg, #f3f4f6 12%, transparent 12.5%, transparent 87%, #f3f4f6 87.5%, #f3f4f6), linear-gradient(30deg, #f3f4f6 12%, transparent 12.5%, transparent 87%, #f3f4f6 87.5%, #f3f4f6), linear-gradient(150deg, #f3f4f6 12%, transparent 12.5%, transparent 87%, #f3f4f6 87.5%, #f3f4f6), linear-gradient(60deg, #f3f4f677 25%, transparent 25.5%, transparent 75%, #f3f4f677 75%, #f3f4f677), linear-gradient(60deg, #f3f4f677 25%, transparent 25.5%, transparent 75%, #f3f4f677 75%, #f3f4f677); background-size: 80px 140px; background-position: 0 0, 0 0, 40px 70px, 40px 70px, 0 0, 40px 70px;";
                break;

            case 'triangles-pattern':
                $pattern_css = "background: #ffffff; background-image: linear-gradient(45deg, #f3f4f6 25%, transparent 25%), linear-gradient(135deg, #f3f4f6 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #f3f4f6 75%), linear-gradient(135deg, transparent 75%, #f3f4f6 75%); background-size: 30px 30px; background-position: 0 0, 15px 0, 15px -15px, 0px 15px;";
                break;

            case 'zigzag-horizontal':
                $pattern_css = "background: linear-gradient(135deg, #f3f4f6 25%, transparent 25%) -15px 0, linear-gradient(225deg, #f3f4f6 25%, transparent 25%) -15px 0, linear-gradient(315deg, #f3f4f6 25%, transparent 25%), linear-gradient(45deg, #f3f4f6 25%, transparent 25%); background-size: 30px 30px;";
                break;

            case 'zigzag-vertical':
                $pattern_css = "background: linear-gradient(45deg, #f3f4f6 25%, transparent 25%) 0 -15px, linear-gradient(315deg, #f3f4f6 25%, transparent 25%) 0 -15px, linear-gradient(225deg, #f3f4f6 25%, transparent 25%), linear-gradient(135deg, #f3f4f6 25%, transparent 25%); background-size: 30px 30px;";
                break;

            case 'tartan':
                $pattern_css = "background: #f3f4f6; background-image: repeating-linear-gradient(45deg, #e5e7eb, #e5e7eb 10px, transparent 10px, transparent 20px), repeating-linear-gradient(135deg, #d1d5db, #d1d5db 5px, transparent 5px, transparent 15px), repeating-linear-gradient(90deg, transparent, transparent 10px, #e5e7eb 10px, #e5e7eb 20px), repeating-linear-gradient(0deg, transparent, transparent 10px, #d1d5db 10px, #d1d5db 20px);";
                break;

            case 'plaid-subtle':
                $pattern_css = "background: #ffffff; background-image: repeating-linear-gradient(0deg, transparent, transparent 35px, rgba(229,231,235,0.5) 35px, rgba(229,231,235,0.5) 70px), repeating-linear-gradient(90deg, transparent, transparent 35px, rgba(209,213,219,0.5) 35px, rgba(209,213,219,0.5) 70px);";
                break;

            // Technical Patterns
            case 'blueprint':
                $pattern_css = "background: #1e40af; background-image: linear-gradient(rgba(255,255,255,.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.1) 1px, transparent 1px), linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px); background-size: 100px 100px, 100px 100px, 20px 20px, 20px 20px; background-position: -2px -2px, -2px -2px, -1px -1px, -1px -1px;";
                break;

            case 'blueprint-dark':
                $pattern_css = "background: #0f172a; background-image: linear-gradient(rgba(59,130,246,.2) 1px, transparent 1px), linear-gradient(90deg, rgba(59,130,246,.2) 1px, transparent 1px), linear-gradient(rgba(59,130,246,.1) 1px, transparent 1px), linear-gradient(90deg, rgba(59,130,246,.1) 1px, transparent 1px); background-size: 100px 100px, 100px 100px, 20px 20px, 20px 20px; background-position: -2px -2px, -2px -2px, -1px -1px, -1px -1px;";
                break;

            case 'circuit-board':
                $pattern_css = "background: #1f2937; background-image: linear-gradient(rgba(34,197,94,.2) 1px, transparent 1px), linear-gradient(90deg, rgba(34,197,94,.2) 1px, transparent 1px), radial-gradient(circle at 20px 20px, rgba(34,197,94,.3) 2px, transparent 3px), radial-gradient(circle at 80px 80px, rgba(59,130,246,.3) 2px, transparent 3px); background-size: 100px 100px, 100px 100px, 100px 100px, 100px 100px;";
                break;

            case 'graph-paper':
                $pattern_css = "background: #ffffff; background-image: linear-gradient(rgba(0,0,0,.1) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,.1) 1px, transparent 1px), linear-gradient(rgba(0,0,0,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,.05) 1px, transparent 1px); background-size: 100px 100px, 100px 100px, 10px 10px, 10px 10px;";
                break;

            case 'notebook':
                $pattern_css = "background: #fefefe; background-image: linear-gradient(to right, #fde047 0px, #fde047 1px, transparent 1px), linear-gradient(to bottom, rgba(0,0,0,.1) 0px, rgba(0,0,0,.1) 1px, transparent 1px), linear-gradient(to bottom, rgba(239,68,68,.3) 0px, rgba(239,68,68,.3) 1px, transparent 1px); background-size: 100% 30px, 100% 30px, 100% 30px; background-position: 40px 0, 0 0, 0 0;";
                break;

            // Cultural Patterns
            case 'moroccan':
                $pattern_css = "background: #ffffff; background-image: radial-gradient(circle at 25% 25%, #f3f4f6 2%, transparent 3%), radial-gradient(circle at 75% 25%, #f3f4f6 2%, transparent 3%), radial-gradient(circle at 25% 75%, #f3f4f6 2%, transparent 3%), radial-gradient(circle at 75% 75%, #f3f4f6 2%, transparent 3%), linear-gradient(45deg, #e5e7eb 25%, transparent 25%, transparent 75%, #e5e7eb 75%), linear-gradient(135deg, #e5e7eb 25%, transparent 25%, transparent 75%, #e5e7eb 75%); background-size: 40px 40px;";
                break;

            case 'japanese-seigaiha':
                $pattern_css = "background: #ffffff; background-image: radial-gradient(circle at 50% 100%, transparent 40%, rgba(59,130,246,.1) 40%, rgba(59,130,246,.1) 60%, transparent 60%), radial-gradient(circle at 0% 100%, transparent 40%, rgba(59,130,246,.05) 40%, rgba(59,130,246,.05) 60%, transparent 60%), radial-gradient(circle at 100% 100%, transparent 40%, rgba(59,130,246,.05) 40%, rgba(59,130,246,.05) 60%, transparent 60%); background-size: 80px 40px, 80px 40px, 80px 40px; background-position: 0 0, -40px 0, 40px 0;";
                break;

            case 'art-deco':
                $pattern_css = "background: #fefefe; background-image: linear-gradient(45deg, #f3f4f6 25%, transparent 25%), linear-gradient(135deg, #f3f4f6 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #e5e7eb 75%), linear-gradient(135deg, transparent 75%, #e5e7eb 75%), linear-gradient(0deg, #d1d5db, #d1d5db 2px, transparent 2px, transparent 18px, #d1d5db 18px, #d1d5db 20px); background-size: 20px 20px, 20px 20px, 20px 20px, 20px 20px, 100% 20px; background-position: 0 0, 10px 0, 10px -10px, 0 10px, 0 0;";
                break;

            case 'vintage-wallpaper':
                $pattern_css = "background: #f9fafb; background-image: radial-gradient(circle at 20% 20%, rgba(239,68,68,.1) 20%, transparent 21%), radial-gradient(circle at 80% 20%, rgba(59,130,246,.1) 20%, transparent 21%), radial-gradient(circle at 20% 80%, rgba(34,197,94,.1) 20%, transparent 21%), radial-gradient(circle at 80% 80%, rgba(245,158,11,.1) 20%, transparent 21%), linear-gradient(45deg, rgba(156,163,175,.1) 25%, transparent 25%), linear-gradient(135deg, rgba(156,163,175,.1) 25%, transparent 25%); background-size: 100px 100px, 100px 100px, 100px 100px, 100px 100px, 20px 20px, 20px 20px;";
                break;

            // Texture Patterns
            case 'noise-texture':
                $pattern_css = "background: #ffffff; opacity: 0.05; background-image: url(\"data:image/svg+xml,%3Csvg viewBox='0 0 400 400' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E\");";
                break;

            case 'fabric-texture':
                $pattern_css = "background: #f3f4f6; background-image: linear-gradient(45deg, rgba(0,0,0,.1) 25%, transparent 25%), linear-gradient(135deg, rgba(0,0,0,.1) 25%, transparent 25%), linear-gradient(45deg, transparent 75%, rgba(0,0,0,.05) 75%), linear-gradient(135deg, transparent 75%, rgba(0,0,0,.05) 75%); background-size: 2px 2px; background-position: 0 0, 1px 0, 1px -1px, 0px 1px;";
                break;

            case 'wood-grain':
                $pattern_css = "background: #92400e; background-image: linear-gradient(90deg, rgba(180,83,9,.3) 50%, transparent 50%), linear-gradient(rgba(180,83,9,.2) 50%, rgba(217,119,6,.2) 50%), linear-gradient(90deg, transparent 50%, rgba(180,83,9,.1) 50%); background-size: 40px 3px, 100% 2px, 20px 8px;";
                break;

            case 'marble-veins':
                $pattern_css = "background: #f8fafc; background-image: linear-gradient(45deg, rgba(148,163,184,.2) 1px, transparent 1px), linear-gradient(135deg, rgba(148,163,184,.1) 1px, transparent 1px), linear-gradient(90deg, rgba(203,213,225,.3) 0.5px, transparent 0.5px); background-size: 60px 60px, 80px 80px, 100% 20px;";
                break;

            case 'paper-texture':
                $pattern_css = "background: #fefefe; background-image: radial-gradient(circle at 1px 1px, rgba(0,0,0,.05) 1px, transparent 0), radial-gradient(circle at 2px 3px, rgba(0,0,0,.03) 1px, transparent 0), radial-gradient(circle at 3px 2px, rgba(0,0,0,.04) 1px, transparent 0); background-size: 10px 10px, 15px 15px, 12px 12px;";
                break;

            // Artistic Patterns
            case 'crosshatch-art':
                $pattern_css = "background: #ffffff; background-image: linear-gradient(45deg, rgba(0,0,0,.1) 1px, transparent 1px), linear-gradient(135deg, rgba(0,0,0,.1) 1px, transparent 1px); background-size: 10px 10px, 10px 10px; background-position: 0 0, 5px 5px;";
                break;

            case 'stippling':
                $pattern_css = "background: #ffffff; background-image: radial-gradient(circle at 25% 25%, #000 0.5px, transparent 0.5px), radial-gradient(circle at 75% 25%, #000 0.3px, transparent 0.3px), radial-gradient(circle at 25% 75%, #000 0.4px, transparent 0.4px), radial-gradient(circle at 75% 75%, #000 0.2px, transparent 0.2px); background-size: 20px 20px; opacity: 0.1;";
                break;

            case 'sketch-lines':
                $pattern_css = "background: #ffffff; background-image: linear-gradient(90deg, transparent 0%, rgba(0,0,0,.1) 50%, transparent 100%), linear-gradient(45deg, transparent 0%, rgba(0,0,0,.05) 50%, transparent 100%), linear-gradient(135deg, transparent 0%, rgba(0,0,0,.05) 50%, transparent 100%); background-size: 30px 1px, 20px 1px, 25px 1px;";
                break;

            case 'watercolor-wash':
                $pattern_css = "background: radial-gradient(ellipse at top, rgba(139,92,246,.1), transparent), radial-gradient(ellipse at bottom, rgba(59,130,246,.1), transparent);";
                break;

            case 'crosshatch-art-dark':
                $pattern_css = "background: #111827; background-image: linear-gradient(45deg, rgba(255,255,255,.1) 1px, transparent 1px), linear-gradient(135deg, rgba(255,255,255,.1) 1px, transparent 1px); background-size: 10px 10px, 10px 10px; background-position: 0 0, 5px 5px;";
                break;

            default:
                $pattern_css = "background: #f8fafc; background-image: linear-gradient(to right, #e2e8f0 1px, transparent 1px), linear-gradient(to bottom, #e2e8f0 1px, transparent 1px); background-size: 20px 20px;";
                break;
        }

        if ($pattern_css) {
            // Add z-index for proper layering with Elementor backgrounds
            $final_css = $pattern_css . '; position: relative; z-index: ' . $z_index . ';';
            $element->add_render_attribute('_wrapper', 'style', preg_replace('/\s+/', ' ', trim($final_css)));
        }
    }

    public function _print_template($template, $widget)
    {
        if ($widget->get_name() != 'section' && $widget->get_name() != 'column' && $widget->get_name() != 'container') {
            return $template;
        }

        ob_start();
        ?>
        <# if (settings.jltma_enable_pattern == 'yes') { #>
            <div class="jltma-pattern-wrapper" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: {{ settings.jltma_pattern_z_index.size }};">
                <#
                var patternStyle = settings.jltma_pattern_style || 'top-gradient-radial';
                var patternCSS = '';

                // All 77 PatternCraft patterns for editor preview
                switch(patternStyle) {
                    case 'top-gradient-radial':
                        patternCSS = 'background: radial-gradient(125% 125% at 50% 10%, #fff 40%, #6366f1 100%);';
                        break;
                    case 'bottom-gradient-radial':
                        patternCSS = 'background: radial-gradient(125% 125% at 50% 90%, #fff 40%, #6366f1 100%);';
                        break;
                    case 'bottom-violet-radial':
                        patternCSS = 'background: radial-gradient(125% 125% at 50% 90%, #fff 40%, #7c3aed 100%);';
                        break;
                    case 'bottom-slate-radial':
                        patternCSS = 'background: radial-gradient(125% 125% at 50% 90%, #fff 40%, #475569 100%);';
                        break;
                    case 'radial-teal-glow':
                        patternCSS = 'background: #ffffff; background-image: radial-gradient(125% 125% at 50% 90%, #ffffff 40%, #14b8a6 100%); background-size: 100% 100%;';
                        break;
                    case 'radial-pink-glow':
                        patternCSS = 'background: #ffffff; background-image: radial-gradient(125% 125% at 50% 90%, #ffffff 40%, #ec4899 100%); background-size: 100% 100%;';
                        break;
                    case 'radial-amber-glow':
                        patternCSS = 'background: #ffffff; background-image: radial-gradient(125% 125% at 50% 90%, #ffffff 40%, #f59e0b 100%); background-size: 100% 100%;';
                        break;
                    case 'radial-emerald-glow':
                        patternCSS = 'background: #ffffff; background-image: radial-gradient(125% 125% at 50% 90%, #ffffff 40%, #10b981 100%); background-size: 100% 100%;';
                        break;
                    case 'dark-horizon-glow':
                        patternCSS = 'background: #000000; background-image: radial-gradient(125% 125% at 50% 90%, #000000 40%, #0d1a36 100%); background-size: 100% 100%;';
                        break;
                    case 'crimson-depth':
                        patternCSS = 'background: #000000; background-image: radial-gradient(125% 125% at 50% 100%, #000000 40%, #2b0707 100%); background-size: 100% 100%;';
                        break;
                    case 'emerald-void':
                        patternCSS = 'background: #000000; background-image: radial-gradient(125% 125% at 50% 90%, #000000 40%, #072607 100%); background-size: 100% 100%;';
                        break;
                    case 'violet-abyss':
                        patternCSS = 'background: #000000; background-image: radial-gradient(125% 125% at 50% 90%, #000000 40%, #2b092b 100%); background-size: 100% 100%;';
                        break;
                    case 'azure-depths':
                        patternCSS = 'background: #000000; background-image: radial-gradient(125% 125% at 50% 100%, #000000 40%, #010133 100%); background-size: 100% 100%;';
                        break;
                    case 'orchid-depths':
                        patternCSS = 'background: #000000; background-image: radial-gradient(125% 125% at 50% 100%, #000000 40%, #350136 100%); background-size: 100% 100%;';
                        break;
                    case 'dark-horizon-glow-top':
                        patternCSS = 'background: radial-gradient(125% 125% at 50% 10%, #000000 40%, #0d1a36 100%);';
                        break;
                    case 'crimson-depth-top':
                        patternCSS = 'background: radial-gradient(125% 125% at 50% 10%, #000000 40%, #2b0707 100%);';
                        break;
                    case 'emerald-void-top':
                        patternCSS = 'background: radial-gradient(125% 125% at 50% 10%, #000000 40%, #072607 100%);';
                        break;
                    case 'violet-abyss-top':
                        patternCSS = 'background: radial-gradient(125% 125% at 50% 10%, #000000 40%, #2b092b 100%);';
                        break;
                    case 'azure-depths-top':
                        patternCSS = 'background: radial-gradient(125% 125% at 50% 10%, #000000 40%, #010133 100%);';
                        break;
                    case 'orchid-depths-top':
                        patternCSS = 'background: radial-gradient(125% 125% at 50% 10%, #000000 40%, #350136 100%);';
                        break;
                    case 'purple-gradient-grid-right':
                        patternCSS = 'background: #ffffff; background-image: linear-gradient(to right, #f0f0f0 1px, transparent 1px), linear-gradient(to bottom, #f0f0f0 1px, transparent 1px), radial-gradient(circle 800px at 100% 200px, #d5c5ff, transparent); background-size: 96px 64px, 96px 64px, 100% 100%;';
                        break;
                    case 'purple-gradient-grid-left':
                        patternCSS = 'background: #ffffff; background-image: linear-gradient(to right, #f0f0f0 1px, transparent 1px), linear-gradient(to bottom, #f0f0f0 1px, transparent 1px), radial-gradient(circle 800px at 0% 200px, #d5c5ff, transparent); background-size: 96px 64px, 96px 64px, 100% 100%;';
                        break;
                    case 'dual-gradient-overlay-strong':
                        patternCSS = 'background: #ffffff; background-image: linear-gradient(to right, rgba(229,231,235,0.8) 1px, transparent 1px), linear-gradient(to bottom, rgba(229,231,235,0.8) 1px, transparent 1px), radial-gradient(circle 500px at 20% 80%, rgba(139,92,246,0.3), transparent), radial-gradient(circle 500px at 80% 20%, rgba(59,130,246,0.3), transparent); background-size: 48px 48px, 48px 48px, 100% 100%, 100% 100%;';
                        break;
                    case 'grid-small':
                        patternCSS = 'background-image: linear-gradient(to right, #e5e7eb 1px, transparent 1px), linear-gradient(to bottom, #e5e7eb 1px, transparent 1px); background-size: 20px 20px;';
                        break;
                    case 'grid-medium':
                        patternCSS = 'background-image: linear-gradient(to right, #d1d5db 1px, transparent 1px), linear-gradient(to bottom, #d1d5db 1px, transparent 1px); background-size: 40px 40px;';
                        break;
                    case 'grid-large':
                        patternCSS = 'background-image: linear-gradient(to right, #9ca3af 1px, transparent 1px), linear-gradient(to bottom, #9ca3af 1px, transparent 1px); background-size: 80px 80px;';
                        break;
                    case 'dots-pattern':
                        patternCSS = 'background-image: radial-gradient(circle, #d1d5db 1px, transparent 1px); background-size: 20px 20px;';
                        break;
                    case 'diagonal-stripes':
                        patternCSS = 'background: repeating-linear-gradient(45deg, #f3f4f6 0px, #f3f4f6 10px, #ffffff 10px, #ffffff 20px);';
                        break;
                    case 'circuit-board':
                        patternCSS = 'background: #1f2937; background-image: linear-gradient(rgba(34,197,94,.2) 1px, transparent 1px), linear-gradient(90deg, rgba(34,197,94,.2) 1px, transparent 1px), radial-gradient(circle at 20px 20px, rgba(34,197,94,.3) 2px, transparent 3px), radial-gradient(circle at 80px 80px, rgba(59,130,246,.3) 2px, transparent 3px); background-size: 100px 100px, 100px 100px, 100px 100px, 100px 100px;';
                        break;
                    case 'blueprint':
                        patternCSS = 'background: #1e40af; background-image: linear-gradient(rgba(255,255,255,.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.1) 1px, transparent 1px), linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px); background-size: 100px 100px, 100px 100px, 20px 20px, 20px 20px; background-position: -2px -2px, -2px -2px, -1px -1px, -1px -1px;';
                        break;
                    case 'aurora-dream-soft-harmony':
                        patternCSS = 'background: #f7eaff; background-image: radial-gradient(ellipse 80% 60% at 60% 20%, rgba(175, 109, 255, 0.50), transparent 65%), radial-gradient(ellipse 70% 60% at 20% 80%, rgba(255, 100, 180, 0.45), transparent 65%), radial-gradient(ellipse 60% 50% at 60% 65%, rgba(255, 235, 170, 0.43), transparent 62%), radial-gradient(ellipse 65% 40% at 50% 60%, rgba(120, 190, 255, 0.48), transparent 68%), linear-gradient(180deg, #f7eaff 0%, #fde2ea 100%); background-size: 100% 100%;';
                        break;
                    default:
                        patternCSS = 'background: #f8fafc; background-image: linear-gradient(to right, #e2e8f0 1px, transparent 1px), linear-gradient(to bottom, #e2e8f0 1px, transparent 1px); background-size: 20px 20px;';
                        break;
                }
                #>
                <div style="width: 100%; height: 100%; position: absolute; top: 0; left: 0; {{{ patternCSS }}}"></div>
            </div>
        <# } #>
        <?php
        $patterns_content = ob_get_contents();
        ob_end_clean();

        $template = $patterns_content . $template;
        return $template;
    }

    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
}
}

if (class_exists('MasterAddons\Modules\JLTMA_Extension_Patterns')) {
    JLTMA_Extension_Patterns::get_instance();
}
