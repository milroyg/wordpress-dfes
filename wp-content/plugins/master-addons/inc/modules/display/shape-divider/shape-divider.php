<?php

namespace MasterAddons\Modules;

use Elementor\Controls_Manager;
use Elementor\Element_Base;
use Elementor\Shapes;

defined('ABSPATH') || die();

if (!class_exists('MasterAddons\Modules\Shape_Divider')) {
class Shape_Divider
{

    public static function init()
    {
        add_filter('elementor/shapes/additional_shapes', [__CLASS__, 'additional_shape_divider']);
        add_action('elementor/element/section/section_shape_divider/before_section_end', [__CLASS__, 'update_shape_list']);
    }

    public static function update_shape_list(Element_Base $element)
    {
        $default_shapes = [];
        $jltma_shapes_top = [];
        $jltma_shapes_bottom = [];

        foreach (Shapes::get_shapes() as $shape_name => $shape_props) {
            if (!isset($shape_props['jltma_shape'])) {
                $default_shapes[$shape_name] = $shape_props['title'];
            } elseif (!isset($shape_props['jltma_shape_bottom'])) {
                $jltma_shapes_top[$shape_name] = $shape_props['title'];
            } else {
                $jltma_shapes_bottom[$shape_name] = $shape_props['title'];
            }
        }

        $element->update_control(
            'shape_divider_top',
            [
                'type' => Controls_Manager::SELECT,
                'groups' => [
                    [
                        'label' => __('Disable', 'master-addons' ),
                        'options' => [
                            '' => __('None', 'master-addons' ),
                        ],
                    ],
                    [
                        'label' => __('Default Shapes', 'master-addons' ),
                        'options' => $default_shapes,
                    ],
                    [
                        'label' => __('Master Shapes', 'master-addons' ),
                        'options' => $jltma_shapes_top,
                    ],
                ],
            ]
        );

        $element->update_control(
            'shape_divider_bottom',
            [
                'type' => Controls_Manager::SELECT,
                'groups' => [
                    [
                        'label' => __('Disable', 'master-addons' ),
                        'options' => [
                            '' => __('None', 'master-addons' ),
                        ],
                    ],
                    [
                        'label' => __('Default Shapes', 'master-addons' ),
                        'options' => $default_shapes,
                    ],
                    [
                        'label' => __('Master Shapes', 'master-addons' ),
                        'options' => array_merge($jltma_shapes_top, $jltma_shapes_bottom),
                    ],
                ],
            ]
        );
    }

    /**
     * Undocumented function
     *
     * @param array $shape_list
     * @return void
     */
    public static function additional_shape_divider($shape_list)
    {
        $jltma_shapes = [
            'abstract-web' => [
                'title' => _x('Abstract Web', 'Shapes', 'master-addons' ),
                'path' => JLTMA_PATH . 'assets/imgs/shape-divider/abstract-web.svg',
                'url' => JLTMA_ASSETS . 'imgs/shape-divider/abstract-web.svg',
                'has_flip' => true,
                'has_negative' => false,
                'jltma_shape' => true,
            ],
            'crossline' => [
                'title' => _x('Crossline', 'Shapes', 'master-addons' ),
                'path' => JLTMA_PATH . 'assets/imgs/shape-divider/crossline.svg',
                'url' => JLTMA_ASSETS . 'imgs/shape-divider/crossline.svg',
                'has_flip' => true,
                'has_negative' => false,
                'jltma_shape' => true,
            ],
            'droplet' => [
                'title' => _x('Droplet', 'Shapes', 'master-addons' ),
                'path' => JLTMA_PATH . 'assets/imgs/shape-divider/droplet.svg',
                'url' => JLTMA_ASSETS . 'imgs/shape-divider/droplet.svg',
                'has_flip' => true,
                'has_negative' => true,
                'jltma_shape' => true,
            ],
            'flame' => [
                'title' => _x('Flame', 'Shapes', 'master-addons' ),
                'path' => JLTMA_PATH . 'assets/imgs/shape-divider/flame.svg',
                'url' => JLTMA_ASSETS . 'imgs/shape-divider/flame.svg',
                'has_flip' => true,
                'has_negative' => false,
                'jltma_shape' => true,
            ],
            'frame' => [
                'title' => _x('Frame', 'Shapes', 'master-addons' ),
                'path' => JLTMA_PATH . 'assets/imgs/shape-divider/frame.svg',
                'url' => JLTMA_ASSETS . 'imgs/shape-divider/frame.svg',
                'has_flip' => true,
                'has_negative' => true,
                'jltma_shape' => true,
            ],
            'half-circle' => [
                'title' => _x('Half Circle', 'Shapes', 'master-addons' ),
                'path' => JLTMA_PATH . 'assets/imgs/shape-divider/half-circle.svg',
                'url' => JLTMA_ASSETS . 'imgs/shape-divider/half-circle.svg',
                'has_flip' => true,
                'has_negative' => true,
                'jltma_shape' => true,
            ],
            'multi-cloud' => [
                'title' => _x('Multi Cloud', 'Shapes', 'master-addons' ),
                'path' => JLTMA_PATH . 'assets/imgs/shape-divider/multi-cloud.svg',
                'url' => JLTMA_ASSETS . 'imgs/shape-divider/multi-cloud.svg',
                'has_flip' => true,
                'has_negative' => false,
                'jltma_shape' => true,
            ],
            'multi-wave' => [
                'title' => _x('Multi Wave', 'Shapes', 'master-addons' ),
                'path' => JLTMA_PATH . 'assets/imgs/shape-divider/multi-wave.svg',
                'url' => JLTMA_ASSETS . 'imgs/shape-divider/multi-wave.svg',
                'has_flip' => true,
                'has_negative' => false,
                'jltma_shape' => true,
            ],
            'smooth-zigzag' => [
                'title' => _x('Smooth Zigzag', 'Shapes', 'master-addons' ),
                'path' => JLTMA_PATH . 'assets/imgs/shape-divider/smooth-zigzag.svg',
                'url' => JLTMA_ASSETS . 'imgs/shape-divider/smooth-zigzag.svg',
                'has_flip' => true,
                'has_negative' => false,
                'jltma_shape' => true,
            ],
            'splash' => [
                'title' => _x('Splash', 'Shapes', 'master-addons' ),
                'path' => JLTMA_PATH . 'assets/imgs/shape-divider/splash.svg',
                'url' => JLTMA_ASSETS . 'imgs/shape-divider/splash.svg',
                'has_flip' => true,
                'has_negative' => true,
                'jltma_shape' => true,
            ],
            'splash2' => [
                'title' => _x('Splash 2', 'Shapes', 'master-addons' ),
                'path' => JLTMA_PATH . 'assets/imgs/shape-divider/splash2.svg',
                'url' => JLTMA_ASSETS . 'imgs/shape-divider/splash2.svg',
                'has_flip' => true,
                'has_negative' => true,
                'jltma_shape' => true,
            ],
            'torn-paper' => [
                'title' => _x('Torn Paper', 'Shapes', 'master-addons' ),
                'path' => JLTMA_PATH . 'assets/imgs/shape-divider/torn-paper.svg',
                'url' => JLTMA_ASSETS . 'imgs/shape-divider/torn-paper.svg',
                'has_flip' => true,
                'has_negative' => false,
                'jltma_shape' => true,
            ],
            'brush' => [
                'title' => _x('Brush', 'Shapes', 'master-addons' ),
                'path' => JLTMA_PATH . 'assets/imgs/shape-divider/brush.svg',
                'url' => JLTMA_ASSETS . 'imgs/shape-divider/brush.svg',
                'has_flip' => true,
                'has_negative' => true,
                'jltma_shape' => true,
            ],
            'sports' => [
                'title' => _x('Sports', 'Shapes', 'master-addons' ),
                'path' => JLTMA_PATH . 'assets/imgs/shape-divider/sports.svg',
                'url' => JLTMA_ASSETS . 'imgs/shape-divider/sports.svg',
                'has_flip' => true,
                'has_negative' => false,
                'jltma_shape' => true,
                'jltma_shape_bottom' => true,
            ],
            'landscape' => [
                'title' => _x('Landscape', 'Shapes', 'master-addons' ),
                'path' => JLTMA_PATH . 'assets/imgs/shape-divider/landscape.svg',
                'url' => JLTMA_ASSETS . 'imgs/shape-divider/landscape.svg',
                'has_flip' => true,
                'has_negative' => false,
                'jltma_shape' => true,
                'jltma_shape_bottom' => true,
            ],
            'nature' => [
                'title' => _x('Nature', 'Shapes', 'master-addons' ),
                'path' => JLTMA_PATH . 'assets/imgs/shape-divider/nature.svg',
                'url' => JLTMA_ASSETS . 'imgs/shape-divider/nature.svg',
                'has_flip' => true,
                'has_negative' => false,
                'jltma_shape' => true,
                'jltma_shape_bottom' => true,
            ],
            'desert' => [
                'title' => _x('Desert', 'Shapes', 'master-addons' ),
                'path' => JLTMA_PATH . 'assets/imgs/shape-divider/desert.svg',
                'url' => JLTMA_ASSETS . 'imgs/shape-divider/desert.svg',
                'has_flip' => true,
                'has_negative' => false,
                'jltma_shape' => true,
                'jltma_shape_bottom' => true,
            ],
            'under-water' => [
                'title' => _x('Under Water', 'Shapes', 'master-addons' ),
                'path' => JLTMA_PATH . 'assets/imgs/shape-divider/under-water.svg',
                'url' => JLTMA_ASSETS . 'imgs/shape-divider/under-water.svg',
                'has_flip' => true,
                'has_negative' => false,
                'jltma_shape' => true,
                'jltma_shape_bottom' => true,
            ],
            'cityscape-layer' => [
                'title' => _x('Cityscape Layer', 'Shapes', 'master-addons' ),
                'path' => JLTMA_PATH . 'assets/imgs/shape-divider/cityscape-layer.svg',
                'url' => JLTMA_ASSETS . 'imgs/shape-divider/cityscape-layer.svg',
                'has_flip' => true,
                'has_negative' => false,
                'jltma_shape' => true,
                'jltma_shape_bottom' => true,
            ],
            'drop' => [
                'title' => _x('Drop', 'Shapes', 'master-addons' ),
                'path' => JLTMA_PATH . 'assets/imgs/shape-divider/drop.svg',
                'url' => JLTMA_ASSETS . 'imgs/shape-divider/drop.svg',
                'has_flip' => true,
                'has_negative' => false,
                'jltma_shape' => true,
            ],
            'mosque' => [
                'title' => _x('Mosque', 'Shapes', 'master-addons' ),
                'path' => JLTMA_PATH . 'assets/imgs/shape-divider/mosque.svg',
                'url' => JLTMA_ASSETS . 'imgs/shape-divider/mosque.svg',
                'has_flip' => true,
                'has_negative' => false,
                'jltma_shape' => true,
            ]
        ];

        /*
		 * svg path should contain elementor class to show in editor mode
		*/
        return array_merge($jltma_shapes, $shape_list);
    }
}
}

if (class_exists('MasterAddons\Modules\Shape_Divider')) {
    Shape_Divider::init();
}
