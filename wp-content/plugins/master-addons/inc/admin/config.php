<?php

/**
 * Master Addons - Unified Configuration
 *
 * Single source of truth for all widgets, extensions, icons, and plugins.
 * Addons and extensions are organized by groups for easier management.
 *
 * @package MasterAddons
 * @since 2.1.0
 */

namespace MasterAddons\Inc\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class Config
{
    private static $instance = null;
    private static $config = null;
    private static $ribbon = null;
    /**
     * Get complete configuration
     *
     * @return array
     */
    public static function get_config()
    {
        if (self::$config !== null) {
            return self::$config;
        }

        self::$ribbon = [
            'new'      => 'New',
            'popular'  => 'Popular',
            'upcoming' => 'Upcoming',
            'featured' => 'Featured',
            'hot'      => 'Hot',
            'updated'  => 'Updated'
        ];

        self::$config = [
            'ribbons' => self::$ribbon,

            /**
             * UI Groups for Dashboard Display
             * Each group can have optional subcategories for better organization
             */
            'addons_category' => [
                'basic' => [
                    'title' => 'Basic',
                    'icon'  => 'eicon-apps',
                    'order' => 10,
                    'addons' => [
                        'ma-dual-heading' => [
                            'title'    => 'Dual Heading',
                            'icon'     => 'eicon-heading',
                            'class'    => 'MasterAddons\Addons\Dual_Heading',
                            'demo_url' => 'https://master-addons.com/element/dual-heading/',
                            'docs_url' => 'https://master-addons.com/docs/addons/dual-heading/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=kXyvNe6l0Sg',
                            'tuts_features' => [
                                "Two-part heading with independent colors",
                                "Separate typography for each heading part",
                                "Add description text below heading",
                                "Fully responsive layout and styling"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                            ],
                        ],
                        'ma-gradient-headline' => [
                            'title'    => 'Gradient Headline',
                            'icon'     => 'eicon-heading',
                            'class'    => 'MasterAddons\Addons\Gradient_Headline',
                            'demo_url' => 'https://master-addons.com/element/gradient-headline/',
                            'docs_url' => 'https://master-addons.com/docs/addons/how-to-add-gradient-headline-in-elementor/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=NgayEI4CthU',
                            'tuts_features' => [
                                "Multi-color gradient text headings",
                                "Fully responsive gradient design",
                                "No coding needed to configure",
                                "Custom gradient angle and colors"
                            ],
                            'is_pro'   => false,
                            'ribbons' => [ 'hot' ]
                        ],
                        'ma-creative-buttons' => [
                            'title'    => 'Creative Button',
                            'icon'     => 'eicon-button',
                            'class'    => 'MasterAddons\Addons\Creative_Button',
                            'demo_url' => 'https://master-addons.com/element/creative-button/',
                            'docs_url' => 'https://master-addons.com/docs/addons/creative-button/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=kFq8l6wp1iI',
                            'tuts_features' => [
                                "30+ unique button styles available",
                                "Hover effects and icon support",
                                "Text alignment and border radius control",
                                "Full color and typography styling"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                            ],
                            'ribbons' => [ 'hot' ]
                        ],
                        'ma-creative-links' => [
                            'title'    => 'Creative Links',
                            'icon'     => 'eicon-editor-external-link',
                            'class'    => 'MasterAddons\Addons\Creative_Links',
                            'demo_url' => 'https://master-addons.com/element/creative-links/',
                            'docs_url' => 'https://master-addons.com/docs/addons/how-to-add-creative-links/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=o6SmdwMJPyA',
                            'tuts_features' => [
                                "Animated hyperlinks with 3D effects",
                                "SVG icon support included",
                                "Custom hover state controls",
                                "Multiple animation style options"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                            ],
                        ],
                        // 'ma-dropdown-button' => [
                        //     'title'    => 'Dropdown Button',
                        //     'icon'     => 'eicon-dual-button',
                        //     'class'    => 'MasterAddons\Addons\Dropdown_Button',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false,
                        //     'assets'   => [
                        //         'css' => true,
                        //     ],
                        // ],
                        // 'ma-dual-button' => [
                        //     'title'    => 'Dual Button',
                        //     'icon'     => 'eicon-dual-button',
                        //     'class'    => 'MasterAddons\Addons\Dual_Button',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false,
                        //     'assets'   => [
                        //         'css' => true,
                        //     ],
                        // ],
                        // 'ma-floating-button' => [
                        //     'title'    => 'Floating Button',
                        //     'icon'     => 'eicon-button',
                        //     'class'    => 'MasterAddons\Addons\Floating_Button',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        //     'assets'   => [
                        //         'css' => true,
                        //     ],
                        // ],
                        'ma-counter-up' => [
                            'title'    => 'Counter Up',
                            'icon'     => 'eicon-counter',
                            'class'    => 'MasterAddons\Addons\Counter_Up',
                            'demo_url' => 'https://master-addons.com/element/counter-up/',
                            'docs_url' => 'https://master-addons.com/docs/addons/counter-up-for-elementor/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=9amvO6p9kpM',
                            'tuts_features' => [
                                "Animated number counter on scroll",
                                "Number prefix and suffix support",
                                "Responsive column layout control",
                                "Custom icon and color per counter"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                                'js'  => true,
                                'vendors' => ['common'],
                            ],
                        ],
                        'ma-countdown-timer' => [
                            'title'    => 'Countdown Timer',
                            'icon'     => 'eicon-countdown',
                            'class'    => 'MasterAddons\Addons\Countdown_Timer',
                            'demo_url' => 'https://master-addons.com/element/countdown-timer/',
                            'docs_url' => 'https://master-addons.com/docs/addons/count-down-timer/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=1lIbOLM9C1I',
                            'tuts_features' => [
                                "Evergreen and scheduled timer modes",
                                "Days, hours, minutes, seconds display",
                                "Custom expire message or action",
                                "Full typography and style controls"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css'     => true,
                                'js'      => true,
                                'vendors' => ['countdown'],
                            ],
                        ],
                        'ma-progressbar' => [
                            'title'    => 'Progress Bar',
                            'icon'     => 'eicon-skill-bar',
                            'class'    => 'MasterAddons\Addons\Progress_Bar',
                            'demo_url' => 'https://master-addons.com/element/progress-bar/',
                            'docs_url' => 'https://master-addons.com/docs/addons/how-to-create-and-customize-progressbar-in-elementor/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=77-b1moRE8M',
                            'tuts_features' => [
                                "Single animated skill or stat bar",
                                "Multiple bar styles available",
                                "Custom color and label support",
                                "Smooth entrance animation on scroll"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css'     => true,
                                'js'      => true,
                                'vendors' => ['loading-bar'],
                            ],
                        ],
                        'ma-progressbars' => [
                            'title'    => 'Progress Bars',
                            'icon'     => 'eicon-progress-tracker',
                            'class'    => 'MasterAddons\Addons\Progress_Bars',
                            'demo_url' => 'https://master-addons.com/element/progress-bars/',
                            'docs_url' => 'https://master-addons.com/docs/addons/progress-bars-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=Mc9uDWJQMIY',
                            'tuts_features' => [
                                "Display multiple skill bars grouped",
                                "Colorful grouped skill set layout",
                                "Individual color per bar supported",
                                "Responsive columns and spacing control"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                                'js'  => true,
                            ],
                        ],
                        // 'ma-piechart' => [
                        //     'title'    => 'Pie Chart',
                        //     'icon'     => 'eicon-counter-circle',
                        //     'class'    => 'MasterAddons\Addons\Piechart',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        //     'pro_features' => [
                        //         'feature 1',
                        //         'feature 2',
                        //         'feature 3',
                        //         'feature 4'
                        //     ],
                        // ],
                        'ma-infobox' => [
                            'title'    => 'Infobox',
                            'icon'     => 'eicon-info-box',
                            'class'    => 'MasterAddons\Addons\Infobox',
                            'demo_url' => 'https://master-addons.com/element/infobox/',
                            'docs_url' => 'https://master-addons.com/docs/addons/infobox-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=2-ymXAZfrF0',
                            'tuts_features' => [
                                "10+ design variations available",
                                "Icon, heading, and description controls",
                                "Hover effects and link support",
                                "Full color and typography styling"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                            ],
                        ],
                        'ma-flipbox' => [
                            'title'    => 'Flipbox',
                            'icon'     => 'eicon-flip-box',
                            'class'    => 'MasterAddons\Addons\Flipbox',
                            'demo_url' => 'https://master-addons.com/element/flipbox/',
                            'docs_url' => 'https://master-addons.com/docs/addons/how-to-configure-flipbox-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=f-B35-xWqF0',
                            'tuts_features' => [
                                "Two-sided flip card on hover",
                                "10 unique flip animation styles",
                                "Custom front and back content",
                                "Icon, image, and text support"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                            ],
                        ],
                        'ma-tooltip' => [
                            'title'    => 'Tooltip',
                            'icon'     => 'eicon-tools',
                            'class'    => 'MasterAddons\Addons\Tooltip',
                            'demo_url' => 'https://master-addons.com/element/tooltip/',
                            'docs_url' => 'https://master-addons.com/docs/addons/adding-tooltip-in-elementor-editor/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=Av3eTae9vaE',
                            'tuts_features' => [
                                "Hover or tap triggered tooltips",
                                "Works on text, icons, and images",
                                "Customizable position and animation",
                                "Full style and color controls"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css'     => true,
                                'js'      => true,
                                'vendors' => ['tippy'],
                            ],
                        ],
                        'ma-blockquote' => [
                            'title'    => 'Blockquote',
                            'icon'     => 'eicon-blockquote',
                            'class'    => 'MasterAddons\Addons\Blockquote',
                            'demo_url' => 'https://master-addons.com/element/blockquote/',
                            'docs_url' => 'https://master-addons.com/docs/addons/blockquote-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=sSCULgPFSHU',
                            'tuts_features' => [
                                "Styled pull quotes with decorative marks",
                                "Author name and bio support",
                                "Author bar symbol customization",
                                "Full typography and color controls"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                            ],
                        ],
                        'ma-business-hours' => [
                            'title'    => 'Business Hours',
                            'icon'     => 'eicon-clock-o',
                            'class'    => 'MasterAddons\Addons\Business_Hours',
                            'demo_url' => 'https://master-addons.com/element/business-hours/',
                            'docs_url' => 'https://master-addons.com/docs/addons/business-hours-elementor/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=x0_HY9uYgog',
                            'tuts_features' => [
                                "Weekly business hours table display",
                                "Highlight current day automatically",
                                "Custom open and closed labels",
                                "Placeable anywhere on your site"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                                'vendors' => ['common'],
                            ],
                            'ribbons' => [ 'popular' ]
                        ],
                        // 'ma-cards' => [
                        //     'title'    => 'Cards',
                        //     'icon'     => 'eicon-image-box',
                        //     'class'    => 'MasterAddons\Addons\Cards',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false,
                        //     'assets'   => [
                        //         'css' => true,
                        //     ],
                        // ],
                        // 'ma-google-maps' => [
                        //     'title'    => 'Google Maps',
                        //     'icon'     => 'eicon-google-maps',
                        //     'class'    => 'MasterAddons\Addons\Google_Maps',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],
                        'ma-accordion' => [
                            'title'    => 'Advanced Accordion',
                            'icon'     => 'eicon-accordion',
                            'class'    => 'MasterAddons\Addons\Advanced_Accordion',
                            'demo_url' => 'https://master-addons.com/element/advanced-accordion/',
                            'docs_url' => 'https://master-addons.com/docs/addons/elementor-accordion-widget/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=rdrqWa-tf6Q',
                            'tuts_features' => [
                                "Nested accordions with toggle icons",
                                "Custom icons per accordion item",
                                "Expand and collapse all controls",
                                "Border styling and color options"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                                'js'  => true,
                            ],
                            'ribbons' => [ 'popular' ]
                        ],
                        'ma-tabs' => [
                            'title'    => 'Tabs',
                            'icon'     => 'eicon-tabs',
                            'class'    => 'MasterAddons\Addons\Tabs',
                            'demo_url' => 'https://master-addons.com/element/tabs/',
                            'docs_url' => 'https://master-addons.com/docs/addons/tabs-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=lsqGmIrdahw',
                            'tuts_features' => [
                                "Interactive content tabs layout",
                                "Fully responsive tab design",
                                "Custom icon support per tab",
                                "Deep typography and color controls"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                                'js'  => true,
                                'vendors' => ['common'],
                            ],
                            'ribbons' => [ 'popular' ]
                        ],
                        'ma-team-members' => [
                            'title'    => 'Team Member',
                            'icon'     => 'eicon-person',
                            'class'    => 'MasterAddons\Addons\Team_Member',
                            'demo_url' => 'https://master-addons.com/element/team-member/',
                            'docs_url' => 'https://master-addons.com/docs/addons/adding-team-members-in-elementor-page-builder/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=wXPEl93_UBw',
                            'tuts_features' => [
                                "Showcase team profiles with bios",
                                "Display photo and social links",
                                "Custom typography and color styling",
                                "Flexible layout and image controls"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                            ],
                        ],
                        'ma-changelog' => [
                            'title'    => 'Changelogs',
                            'icon'     => 'eicon-history',
                            'class'    => 'MasterAddons\Addons\Changelogs',
                            'demo_url' => 'https://master-addons.com/element/changelogs/',
                            'docs_url' => 'https://master-addons.com/docs/addons/changelog-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=qWRgJkFfBow',
                            'tuts_features' => [
                                "Structured changelog for digital products",
                                "Version badges with custom labels",
                                "Full typography and color controls",
                                "Clean and organized layout design"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css'     => true,
                                'vendors' => ['common']
                            ],
                        ],
                    ],
                ],
                'content' => [
                    'title' => 'Content',
                    'icon'  => 'eicon-post-content',
                    'order' => 20,
                    'addons' => [
                        'ma-blog' => [
                            'title'    => 'Blog',
                            'icon'     => 'eicon-posts-grid',
                            'class'    => 'MasterAddons\Addons\Blog',
                            'demo_url' => 'https://master-addons.com/element/blog/',
                            'docs_url' => 'https://master-addons.com/docs/addons/blog-element-customization/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=03AcgVEsTaA',
                            'tuts_features' => [
                                "Grid, list, and card blog layouts",
                                "Category filtering and post queries",
                                "Custom post count and order",
                                "Responsive design with style controls"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css'     => true,
                                'js'      => true,
                                'vendors' => ['common', 'swiper', 'swiper-carousel', 'isotope', 'masonry'],
                            ],
                            'ribbons' => [ 'featured' ]
                        ],
                        'ma-news-ticker' => [
                            'title'    => 'News Ticker',
                            'icon'     => 'eicon-posts-ticker',
                            'class'    => 'MasterAddons\Addons\News_Ticker',
                            'demo_url' => 'https://master-addons.com/element/news-ticker/',
                            'docs_url' => 'https://master-addons.com/docs/addons/news-ticker-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=jkrBCzebQ-E',
                            'tuts_features' => [
                                "Swiper-powered scrolling news ticker",
                                "Thumbnail, title, and height controls",
                                "Custom thumbnail shape options",
                                "Speed and direction control support"
                            ],
                            'is_pro'   => true,
                            'ribbons' => [ 'hot' ]
                        ],
                        'ma-timeline' => [
                            'title'    => 'Timeline',
                            'icon'     => 'eicon-time-line',
                            'class'    => 'MasterAddons\Addons\Timeline',
                            'demo_url' => 'https://master-addons.com/element/timeline/',
                            'docs_url' => 'https://master-addons.com/docs/addons/timeline-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=0mcDMKrH1A0',
                            'tuts_features' => [
                                "Horizontal and vertical timeline layout",
                                "Supports post and custom timeline types",
                                "Icon and image per timeline item",
                                "Full color and typography controls"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css'     => true,
                                'js'      => true,
                                'vendors' => ['common', 'animation', 'swiper', 'swiper-carousel'],
                            ],
                        ],
                        'ma-toggle-content' => [
                            'title'    => 'Toggle Content',
                            'icon'     => 'eicon-dual-button',
                            'class'    => 'MasterAddons\Addons\Toggle_Content',
                            'demo_url' => 'https://master-addons.com/element/toggle-content/',
                            'docs_url' => 'https://master-addons.com/docs/addons/toggle-content/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=TH6wbVuWdTA',
                            'tuts_features' => [
                                "Switch between two content sets",
                                "Perfect for monthly/yearly pricing toggle",
                                "Smooth animation on toggle switch",
                                "Custom label and color controls"
                            ],
                            'is_pro'   => true,
                        ],
                        'ma-restrict-content' => [
                            'title'    => 'Restrict Content',
                            'icon'     => 'eicon-lock-user',
                            'class'    => 'MasterAddons\Addons\Restrict_Content',
                            'demo_url' => 'https://master-addons.com/element/restrict-content/',
                            'docs_url' => 'https://master-addons.com/docs/addons/restrict-content-for-elementor/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=Alc1R_W5_Z8',
                            'tuts_features' => [
                                "Gate content by user role",
                                "Password and age verification support",
                                "Custom styled error messages",
                                "Flexible content restriction rules"
                            ],
                            'is_pro'   => true,
                        ],
                        'ma-source-code' => [
                            'title'    => 'Source Code',
                            'icon'     => 'eicon-code',
                            'class'    => 'MasterAddons\Addons\Source_Code',
                            'demo_url' => 'https://master-addons.com/element/source-code/',
                            'docs_url' => 'https://master-addons.com/docs/addons/source-code-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=Yz4m3FI_ccc',
                            'tuts_features' => [
                                "Syntax highlighting for 70+ languages",
                                "One-click copy button included",
                                "Custom theme and font size",
                                "Line numbers toggle support"
                            ],
                            'is_pro'   => true,
                        ],
                        // 'ma-code-highlighter' => [
                        //     'title'    => 'Code Highlighter',
                        //     'icon'     => 'eicon-editor-code',
                        //     'class'    => 'MasterAddons\Addons\Code_Highlighter',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false,
                        // ],
                        'ma-comments' => [
                            'title'    => 'MA Comments',
                            'icon'     => 'eicon-comments',
                            'class'    => 'MasterAddons\Addons\Comments',
                            'demo_url' => 'https://master-addons.com/demos/comments-builder',
                            'docs_url' => 'https://master-addons.com/docs/addons/comments-builder/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=vKglaHoWxQs',
                            'tuts_feature' => [
                                "Add conditional comment form",
                                "Design in Elementor Editor",
                                "Multiple Variation of Form",
                                "Design reply style"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                                'js'  => true,
                            ],
                        ],
                        // 'ma-iframe' => [
                        //     'title'    => 'iFrame',
                        //     'icon'     => 'eicon-frame-expand',
                        //     'class'    => 'MasterAddons\Addons\IFrame',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false,
                        // ],
                    ],
                ],
                'media' => [
                    'title' => 'Media',
                    'icon'  => 'eicon-image',
                    'order' => 30,
                    'addons' => [
                        'ma-advanced-image' => [
                            'title'    => 'Advanced Image',
                            'icon'     => 'eicon-image',
                            'class'    => 'MasterAddons\Addons\Advanced_Image',
                            'demo_url' => 'https://master-addons.com/element/advanced-image/',
                            'docs_url' => 'https://master-addons.com/docs/addons/advanced-image-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=fhdwiiy7JiE',
                            'tuts_features' => [
                                "Swap images on hover effect",
                                "Add stylish ribbons with positions",
                                "Lightbox and tilt effect support",
                                "Border radius and overlay styling"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                            ],
                        ],
                        'ma-image-hover-effects' => [
                            'title'    => 'Image Hover Effects',
                            'icon'     => 'eicon-image-rollover',
                            'class'    => 'MasterAddons\Addons\Image_Hover_Effects',
                            'demo_url' => 'https://master-addons.com/element/image-hover-effects/',
                            'docs_url' => 'https://master-addons.com/docs/addons/image-hover-effects-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=vWGWzuRKIss',
                            'tuts_features' => [
                                "20+ hover animation effects",
                                "Title and description overlays",
                                "Supports Elementor Dynamic Tags",
                                "Custom color and typography controls"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                            ],
                            'ribbons' => [ 'hot' ]
                        ],
                        'ma-image-hotspot' => [
                            'title'    => 'Image Hotspot',
                            'icon'     => 'eicon-image-hotspot',
                            'class'    => 'MasterAddons\Addons\Image_Hotspot',
                            'demo_url' => 'https://master-addons.com/element/image-hotspot/',
                            'docs_url' => 'https://master-addons.com/docs/addons/image-hotspot/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=IDAd_d986Hg',
                            'tuts_features' => [
                                "Click or hover hotspots on images",
                                "Tooltip-style info overlays on spots",
                                "Great for product and feature demos",
                                "Custom icon and pulse animation"
                            ],
                            'is_pro'   => true,
                            'ribbons' => [ 'hot' ]
                        ],
                        'ma-image-comparison' => [
                            'title'    => 'Image Comparison',
                            'icon'     => 'eicon-image-before-after',
                            'class'    => 'MasterAddons\Addons\Image_Comparison',
                            'demo_url' => 'https://master-addons.com/element/image-comparison/',
                            'docs_url' => 'https://master-addons.com/docs/addons/image-comparison-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=3nqRRXSGk3M',
                            'tuts_features' => [
                                "Drag-to-compare before/after slider",
                                "Perfect for photographers and designers",
                                "Custom handle and divider styling",
                                "Vertical and horizontal orientation"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css'     => true,
                                'js'      => true,
                                'vendors' => ['twentytwenty'],
                            ],
                            'ribbons' => [ 'hot' ]
                        ],
                        // 'ma-image-scroll' => [
                        //     'title'    => 'Image Scroll',
                        //     'icon'     => 'eicon-scroll',
                        //     'class'    => 'MasterAddons\Addons\Image_Scroll',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],
                        // 'ma-svg' => [
                        //     'title'    => 'Inline SVG',
                        //     'icon'     => 'eicon-code',
                        //     'class'    => 'MasterAddons\Addons\Inline_Svg',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],
                        'ma-image-filter-gallery' => [
                            'title'    => 'Filterable Image Gallery',
                            'icon'     => 'eicon-gallery-masonry',
                            'class'    => 'MasterAddons\Addons\Filterable_Image_Gallery',
                            'demo_url' => 'https://master-addons.com/element/filterable-image-gallery/',
                            'docs_url' => 'https://master-addons.com/docs/addons/filterable-image-gallery/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=h7egsnX4Ewc',
                            'tuts_features' => [
                                "Category-filtered image gallery layout",
                                "Custom filter labels per category",
                                "Page-link support for gallery items",
                                "Lightbox support for full view"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css'     => true,
                                'js'      => true,
                                'vendors' => ['common', 'fancybox', 'isotope', 'tilt', 'tippy'],
                            ],
                            'ribbons' => [ 'popular' ]
                        ],
                        //     'ma-audio-playlist' => [
                        //         'title'    => 'Audio Playlist',
                        //         'icon'     => 'eicon-headphones',
                        //         'class'    => 'MasterAddons\Addons\Audio_Playlist',
                        //         'demo_url' => '',
                        //         'docs_url' => '',
                        //         'tuts_url' => '',
                        //         'is_pro'   => true,
                        //     ],
                        // 'ma-video-playlist' => [
                        //     'title'    => 'Video Playlist',
                        //     'icon'     => 'eicon-headphones',
                        //     'class'    => 'MasterAddons\Addons\Video_Playlist',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],
                        // 'ma-morphing-blob' => [
                        //     'title'    => 'Morphing Blob',
                        //     'icon'     => 'eicon-shape',
                        //     'class'    => 'MasterAddons\Addons\Morphing_Blob',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],
                        // 'ma-pdf-viewer' => [
                        //     'title'    => 'PDF Viewer',
                        //     'icon'     => 'eicon-document-file',
                        //     'class'    => 'MasterAddons\Addons\PDF_Viewer',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],
                        // 'ma-pdfembed' => [
                        //     'title'    => 'PDF Embed',
                        //     'icon'     => 'eicon-document-file',
                        //     'class'    => 'MasterAddons\Addons\WpmfPdfEmbedElementorWidget',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false,
                        //     'assets'   => [],
                        // ],
                        // 'ma-filedesign' => [
                        //     'title'    => 'File Design',
                        //     'icon'     => 'eicon-folder-o',
                        //     'class'    => 'MasterAddons\Addons\WpmfFileDesignElementorWidget',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false,
                        //     'assets'   => [],
                        // ],
                    ],
                ],
                'slider' => [
                    'title' => 'Slider',
                    'icon'  => 'eicon-slider-push',
                    'order' => 40,
                    'addons' => [
                        // 'ma-slider' => [
                        //     'title'    => 'Slider',
                        //     'icon'     => 'eicon-slider-push',
                        //     'class'    => 'MasterAddons\Addons\Slider',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        //     'assets'   => [
                        //         'css'     => true,
                        //         'vendors' => ['swiper'],
                        //     ],
                        // ],
                        'ma-image-carousel' => [
                            'title'    => 'Image Carousel',
                            'icon'     => 'eicon-media-carousel',
                            'class'    => 'MasterAddons\Addons\Image_Carousel',
                            'demo_url' => 'https://master-addons.com/element/image-carousel/',
                            'docs_url' => 'https://master-addons.com/docs/addons/image-carousel/',
                            'tuts_url' => 'https://youtu.be/WD90-LdHXrU',
                            'tuts_features' => [
                                "Customizable photo carousel layout",
                                "Multiple navigation control options",
                                "Auto-play and loop settings",
                                "Responsive slides per view control"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css'     => true,
                                'js'      => true,
                                'vendors' => ['swiper', 'swiper-carousel', 'fancybox'],
                            ],
                            'ribbons' => [ 'popular' ]
                        ],
                        'ma-gallery-slider' => [
                            'title'    => 'Gallery Slider',
                            'icon'     => 'eicon-gallery-group',
                            'class'    => 'MasterAddons\Addons\Gallery_Slider',
                            'demo_url' => 'https://master-addons.com/element/gallery-slider/',
                            'docs_url' => 'https://master-addons.com/docs/addons/gallery-slider/',
                            'tuts_url' => 'https://youtu.be/ZNhGERmt5u0',
                            'tuts_features' => [
                                "Thumbnail-based photo gallery slider",
                                "Large image preview on selection",
                                "Auto-play and navigation controls",
                                "Custom style and spacing options"
                            ],
                            'is_pro'   => true,
                            'assets'   => [
                                'css'     => true,
                                'js'      => true,
                                'vendors' => ['swiper', 'swiper-carousel'],
                            ],
                            'ribbons' => [ 'popular' ]
                        ],
                        'ma-team-members-slider' => [
                            'title'    => 'Team Slider',
                            'icon'     => 'eicon-person',
                            'class'    => 'MasterAddons\Addons\Team_Slider',
                            'demo_url' => 'https://master-addons.com/element/team-slider/',
                            'docs_url' => 'https://master-addons.com/docs/addons/team-members-carousel/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=ubP_h86bP-c',
                            'tuts_features' => [
                                "Swipeable team member carousel",
                                "Responsive column count controls",
                                "Auto-play and navigation options",
                                "Custom style for each member card"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css'     => true,
                                'js'      => true,
                                'vendors' => ['swiper', 'swiper-carousel', 'gridder'],
                            ],
                            'ribbons' => [ 'popular' ]
                        ],
                        // 'ma-twitter-slider' => [
                        //     'title'    => 'Twitter Slider',
                        //     'icon'     => 'eicon-twitter-feed',
                        //     'class'    => 'MasterAddons\Addons\Twitter_Slider',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],
                        // 'ma-devices-slider' => [
                        //     'title'    => 'Devices Slider',
                        //     'icon'     => 'eicon-device-mobile',
                        //     'class'    => 'MasterAddons\Addons\Devices_Slider',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],
                        'ma-logo-slider' => [
                            'title'    => 'Logo Slider',
                            'icon'     => 'eicon-slider-push',
                            'class'    => 'MasterAddons\Addons\Logo_Slider',
                            'demo_url' => 'https://master-addons.com/element/logo-slider/',
                            'docs_url' => 'https://master-addons.com/docs/addons/logo-slider/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=NHjqO0mbc_s',
                            'tuts_features' => [
                                "Auto-scrolling brand logo strip",
                                "Grayscale to color hover effect",
                                "Custom logo size and spacing",
                                "Responsive slides and speed control"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css'     => true,
                                'js'      => true,
                                'vendors' => ['swiper', 'swiper-carousel'],
                            ],
                            'ribbons' => [ 'popular' ]
                        ],
                    ],
                ],
                'navigation' => [
                    'title' => 'Navigation',
                    'icon'  => 'eicon-nav-menu',
                    'order' => 50,
                    'addons' => [
                        'ma-table-of-contents' => [
                            'title'    => 'Table of Contents',
                            'icon'     => 'eicon-post-list',
                            'class'    => 'MasterAddons\Addons\Table_of_Contents',
                            'demo_url' => 'https://master-addons.com/element/table-of-contents/',
                            'docs_url' => 'https://master-addons.com/docs/addons/ma-table/',
                            'tuts_url' => 'https://youtu.be/gWmeINr5HjQ',
                            'tuts_features' => [
                                "Auto-generated navigation for long content",
                                "Personalized anchor link controls",
                                "Collapsible and sticky TOC support",
                                "Custom heading levels to include"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css'     => true,
                                'js'      => true,
                            ],
                            'ribbons' => [ 'featured' ]
                        ],
                        'ma-off-canvas-content' => [
                            'title'    => 'Off-Canvas Content',
                            'icon'     => 'eicon-sidebar',
                            'class'    => 'MasterAddons\Addons\Off_Canvas_Content',
                            'demo_url' => 'https://master-addons.com/element/off-canvas-content/',
                            'docs_url' => 'https://master-addons.com/docs/addons/off-canvas/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=-weBko7ZMVg',
                            'tuts_features' => [
                                "Slide-in off-canvas sidebar panel",
                                "Triggered by button or menu icon",
                                "Custom width, position, and overlay",
                                "Add any Elementor content inside"
                            ],
                            'is_pro'   => true,
                            'ribbons' => [ 'new' ]
                        ],
                        // 'ma-devices' => [
                        //     'title'    => 'Devices',
                        //     'icon'     => 'eicon-device-mobile',
                        //     'class'    => 'MasterAddons\Addons\Devices',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        //     'assets'   => [
                        //         'css' => ['devices'],
                        //     ],
                        // ],
                        'ma-navmenu' => [
                            'title'    => 'Nav Menu',
                            'icon'     => 'eicon-nav-menu',
                            'class'    => 'MasterAddons\Addons\Nav_Menu',
                            'demo_url' => 'https://master-addons.com/element/nav-menu/',
                            'docs_url' => 'https://master-addons.com/docs/addons/navigation-menu-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=WhA5YnE4yJg',
                            'tuts_features' => [
                                "Fully responsive custom navigation menu",
                                "Use in headers and footers",
                                "Dropdown and mega menu support",
                                "Full color and typography controls"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                                'js'  => true,
                            ],
                            'ribbons' => [ 'featured' ]
                        ],
                        // 'ma-offcanvas-menu' => [
                        //     'title'    => 'Off-Canvas Menu',
                        //     'icon'     => 'eicon-sidebar',
                        //     'class'    => 'MasterAddons\Addons\Offcanvas_Menu',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],
                        // 'ma-post-pagination' => [
                        //     'title'    => 'Post Pagination',
                        //     'icon'     => 'eicon-post-navigation',
                        //     'class'    => 'MasterAddons\Addons\Post_Pagination',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false,
                        //     'assets'   => [
                        //         'css' => true,
                        //     ],
                        // ],
                        // 'ma-quick-contact' => [
                        //     'title'    => 'Quick Contact',
                        //     'icon'     => 'eicon-call-to-action',
                        //     'class'    => 'MasterAddons\Addons\Quick_Contact',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false,
                        //     'assets'   => [
                        //         'css' => true,
                        //     ],
                        // ],
                    ],
                ],
                'forms' => [
                    'title' => 'Forms',
                    'icon'  => 'eicon-form-horizontal',
                    'order' => 70,
                    'addons' => [
                        'contact-form-7' => [
                            'title'    => 'Contact Form 7',
                            'icon'     => 'eicon-mail',
                            'class'    => 'MasterAddons\Addons\Contact_Form_7',
                            'demo_url' => 'https://master-addons.com/element/contact-form-7/',
                            'docs_url' => 'https://master-addons.com/docs/addons/how-to-edit-contact-form-7/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=1fU6lWniRqo',
                            'tuts_features' => [
                                "Style CF7 forms inside Elementor",
                                "10 layout variations available",
                                "Custom input, label, button styling",
                                "Full color and typography controls"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                            ],
                            'ribbons' => [ 'featured' ]
                        ],
                        'ninja-forms' => [
                            'title'    => 'Ninja Form',
                            'icon'     => 'eicon-mail',
                            'class'    => 'MasterAddons\Addons\Ninja_Form',
                            'demo_url' => 'https://master-addons.com/element/ninja-form/',
                            'docs_url' => 'https://master-addons.com/docs/addons/how-to-edit-contact-form-7/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=1fU6lWniRqo',
                            'tuts_features' => [
                                "Full Ninja Forms styling in Elementor",
                                "No Pro plugin required",
                                "Custom field and button styling",
                                "Responsive layout and color controls"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                            ],
                        ],
                        'wpforms' => [
                            'title'    => 'WP Forms',
                            'icon'     => 'eicon-mail',
                            'class'    => 'MasterAddons\Addons\WP_Forms',
                            'demo_url' => 'https://master-addons.com/element/wp-forms/',
                            'docs_url' => 'https://master-addons.com/docs/addons/how-to-edit-contact-form-7/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=1fU6lWniRqo',
                            'tuts_features' => [
                                "Drag-and-drop WPForms styling",
                                "Style forms inside Elementor editor",
                                "Custom input focus and hover state",
                                "Full typography and spacing control"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                            ],
                        ],
                        'gravity-forms' => [
                            'title'    => 'Gravity Forms',
                            'icon'     => 'eicon-mail',
                            'class'    => 'MasterAddons\Addons\Gravity_Forms',
                            'demo_url' => 'https://master-addons.com/element/gravity-forms/',
                            'docs_url' => 'https://master-addons.com/docs/addons/how-to-edit-contact-form-7/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=1fU6lWniRqo',
                            'tuts_features' => [
                                "Brand-matched Gravity Forms styling",
                                "Style forms directly in Elementor",
                                "Custom field borders and colors",
                                "Full typography and button control"
                            ],
                            'is_pro'   => true,
                        ],
                        'caldera-forms' => [
                            'title'    => 'Caldera Forms',
                            'icon'     => 'eicon-mail',
                            'class'    => 'MasterAddons\Addons\Caldera_Forms',
                            'demo_url' => 'https://master-addons.com/element/caldera-forms/',
                            'docs_url' => 'https://master-addons.com/docs/addons/how-to-edit-contact-form-7/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=1fU6lWniRqo',
                            'tuts_features' => [
                                "Mobile-friendly Caldera Forms styling",
                                "No CSS expertise required",
                                "Custom color and field styling",
                                "Responsive layout control support"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                            ],
                        ],
                        'weforms' => [
                            'title'    => 'weForms',
                            'icon'     => 'eicon-mail',
                            'class'    => 'MasterAddons\Addons\Weforms',
                            'demo_url' => 'https://master-addons.com/demos/wp-forms/',
                            'docs_url' => 'https://master-addons.com/docs/addons/how-to-edit-contact-form-7/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=1fU6lWniRqo',
                            'tuts_features' => [
                                "weForms layout customization in Elementor",
                                "Style fields and buttons visually",
                                "Custom color and typography controls",
                                "Responsive design support included"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                            ],
                        ],
                        // 'fluent-form' => [
                        //     'title'    => 'Fluent Form',
                        //     'icon'     => 'eicon-form-horizontal',
                        //     'class'    => 'MasterAddons\Addons\Fluent_Form',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false,
                        //     'assets'   => [
                        //         'css' => true,
                        //     ],
                        // ],
                    ],
                ],
                'marketing' => [
                    'title' => 'Marketing',
                    'icon'  => 'eicon-price-table',
                    'order' => 60,
                    'addons' => [
                        'ma-call-to-action' => [
                            'title'    => 'Call to Action',
                            'icon'     => 'eicon-call-to-action',
                            'class'    => 'MasterAddons\Addons\Call_to_Action',
                            'demo_url' => 'https://master-addons.com/element/call-to-action/',
                            'docs_url' => 'https://master-addons.com/docs/addons/call-to-action/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=iY2q1jtSV5o',
                            'tuts_features' => [
                                "Eye-catching title and description layout",
                                "Customizable CTA button with hover effects",
                                "Multiple design style variations",
                                "Full color and typography controls"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                                'vendors' => ['common'],
                            ],
                        ],
                        'ma-pricing-table' => [
                            'title'    => 'Pricing Table',
                            'icon'     => 'eicon-price-table',
                            'class'    => 'MasterAddons\Addons\Pricing_Table',
                            'demo_url' => 'https://master-addons.com/element/pricing-table/',
                            'docs_url' => 'https://master-addons.com/docs/addons/pricing-table-elementor-free-widget/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=_FUk1EfLBUs',
                            'tuts_features' => [
                                "Toggle switcher for monthly/yearly plans",
                                "Tooltips on individual features",
                                "Gradient buttons and feature list styling",
                                "Highlighted recommended plan support"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css'     => true,
                                'vendors' => ['common']
                            ],
                        ],
                        'ma-comparison-table' => [
                            'title'    => 'Comparison Table',
                            'icon'     => 'eicon-price-table',
                            'class'    => 'MasterAddons\Addons\Comparison_Table',
                            'demo_url' => 'https://master-addons.com/element/comparison-table/',
                            'docs_url' => 'https://master-addons.com/docs/addons/elementor-comparison-table/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=qUkY1YwPz2Y',
                            'tuts_features' => [
                                "Side-by-side feature comparison layout",
                                "Add titles, prices, and checkmarks",
                                "Highlight recommended column option",
                                "Full color and typography styling"
                            ],
                            'is_pro'   => true,
                        ],
                        // 'ma-coupon-code' => [
                        //     'title'    => 'Coupon Code',
                        //     'icon'     => 'eicon-barcode',
                        //     'class'    => 'MasterAddons\Addons\Coupon_Code',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],
                        'ma-mailchimp' => [
                            'title'    => 'Mailchimp',
                            'icon'     => 'eicon-mailchimp',
                            'class'    => 'MasterAddons\Addons\Mailchimp',
                            'demo_url' => 'https://master-addons.com/element/mailchimp/',
                            'docs_url' => 'https://master-addons.com/docs/addons/mailchimp-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=hST5tycqCsw',
                            'tuts_features' => [
                                "Native Mailchimp form integration",
                                "No Pro plugin needed",
                                "Full input and button styling",
                                "Custom success and error messages"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                            ],
                        ],
                        'ma-domain-checker' => [
                            'title'    => 'Domain Search',
                            'icon'     => 'eicon-check-circle',
                            'class'    => 'MasterAddons\Addons\Domain_Search',
                            'demo_url' => 'https://master-addons.com/element/domain-search/',
                            'docs_url' => 'https://master-addons.com/docs/addons/how-ma-domain-checker-works/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=eRjGflYhHdY',
                            'tuts_features' => [
                                "White-label domain search tool",
                                "Ideal for hosting and affiliates",
                                "Custom button and input styling",
                                "TLD filter and results display"
                            ],
                            'is_pro'   => true,
                        ],
                        'ma-popup-trigger' => [
                            'title'    => 'Popup Trigger',
                            'icon'     => 'eicon-button',
                            'class'    => 'MasterAddons\Addons\Popup_Trigger',
                            'demo_url' => 'https://master-addons.com/demos/popup-trigger/',
                            'docs_url' => 'https://master-addons.com/docs/addons/popup-trigger/',
                            'tuts_url' => '',
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                            ],
                            'ribbons' => [ 'new' ]
                        ],
                        // 'ma-social-share' => [
                        //     'title'    => 'Social Share',
                        //     'icon'     => 'eicon-share',
                        //     'class'    => 'MasterAddons\Addons\Social_Share',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false,
                        //     'assets'   => [
                        //         'css' => true,
                        //     ],
                        // ],
                        // 'ma-calendar' => [
                        //     'title'    => 'Calendar',
                        //     'icon'     => 'eicon-calendar',
                        //     'class'    => 'MasterAddons\Addons\Calendar',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],
                        // 'ma-pros-and-cons' => [
                        //     'title'    => 'Pros and Cons',
                        //     'icon'     => 'eicon-columns',
                        //     'class'    => 'MasterAddons\Addons\Pros_Cons',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],
                    ],
                ],
                'animation' => [
                    'title' => 'Animation',
                    'icon'  => 'eicon-animation',
                    'order' => 70,
                    'addons' => [
                        'ma-animated-headlines' => [
                            'title'    => 'Animated Headlines',
                            'icon'     => 'eicon-animated-headline',
                            'class'    => 'MasterAddons\Addons\Animated_Headlines',
                            'demo_url' => 'https://master-addons.com/element/animated-headlines/',
                            'docs_url' => 'https://master-addons.com/docs/addons/animated-headline-elementor-page-builder/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=09QIUPdUQnM',
                            'tuts_features' => [
                                "Multiple dynamic text animation styles",
                                "Customizable animation delay and speed",
                                "Loading bar with color controls",
                                "Fully responsive typography settings"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                                'js'  => true,
                            ],
                            'ribbons' => [ 'hot' ]
                        ],

                        // 'ma-scroll-indicator' => [
                        //     'title'    => 'Scroll Indicator',
                        //     'icon'     => 'eicon-scroll',
                        //     'class'    => 'MasterAddons\Addons\Scroll_Indicator',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],  
                    ],
                ],
                'dynamic' => [
                    'title' => 'Dynamic',
                    'icon'  => 'eicon-flash',
                    'order' => 80,
                    'addons' => [
                        'ma-current-time' => [
                            'title'    => 'Current Time',
                            'icon'     => 'eicon-clock-o',
                            'class'    => 'MasterAddons\Addons\Current_Time',
                            'demo_url' => 'https://master-addons.com/element/current-time/',
                            'docs_url' => 'https://master-addons.com/docs/addons/current-time/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=Icwi5ynmzkQ',
                            'tuts_features' => [
                                "Live clock widget in real-time",
                                "Customizable time and date format",
                                "Styled like a newspaper masthead",
                                "Full typography and color control"
                            ],
                            'is_pro'   => false,
                            // 'assets'   => [
                            //     'css' => true,
                            // ],
                        ],
                        // 'ma-site-logo' => [
                        //     'title'    => 'Site Logo',
                        //     'icon'     => 'eicon-site-logo',
                        //     'class'    => 'MasterAddons\Addons\Site_Logo',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false,
                        //     'assets'   => [
                        //         'css' => true,
                        //     ],
                        // ],
                        // 'ma-data-table' => [
                        //     'title'    => 'Data Table',
                        //     'icon'     => 'eicon-table',
                        //     'class'    => 'MasterAddons\Addons\Data_Table',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false,
                        //     'assets'   => [
                        //         'css' => true,
                        //     ],
                        // ],
                        'ma-table' => [
                            'title'    => 'Dynamic Table',
                            'icon'     => 'eicon-table',
                            'class'    => 'MasterAddons\Addons\Dynamic_Table',
                            'demo_url' => 'https://master-addons.com/element/dynamic-table/',
                            'docs_url' => 'https://master-addons.com/docs/addons/dynamic-table-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=bn0TvaGf9l8',
                            'tuts_features' => [
                                "Drag-and-drop table builder",
                                "Add images, colors, and columns",
                                "Odd/even row coloring support",
                                "Border radius and non-responsive mode"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                                'vendors' => ['common'],
                            ],
                        ],
                        // 'ma-chatgpt' => [
                        //     'title'    => 'ChatGPT',
                        //     'icon'     => 'eicon-ai',
                        //     'class'    => 'MasterAddons\Addons\CHATGPT',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],
                        'ma-search' => [
                            'title'    => 'Search',
                            'icon'     => 'eicon-search',
                            'class'    => 'MasterAddons\Addons\Search',
                            'demo_url' => 'https://master-addons.com/element/search/',
                            'docs_url' => 'https://master-addons.com/docs/addons/search-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=Uk6nnoN5AJ4',
                            'tuts_features' => [
                                "Standalone or popup search bar",
                                "Instant site-wide search results",
                                "Custom placeholder and icon styling",
                                "Fully responsive design support"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => true,
                                'js'  => true,
                            ],
                        ],

                    ],
                ],
                'woocommerce' => [
                    'title' => 'WooCommerce',
                    'icon'  => 'eicon-woocommerce',
                    'order' => 80,
                    'addons' => [
                        // 'ma-product-listing' => [
                        //     'title'    => 'Product Listing',
                        //     'icon'     => 'eicon-product-tabs',
                        //     'class'    => 'MasterAddons\Addons\Product_Listing',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],
                        'ma-featured-product' => [
                            'title'    => 'Featured Product',
                            'icon'     => 'eicon-image-box',
                            'class'    => 'MasterAddons\Addons\Featured_Product',
                            'demo_url' => 'https://master-addons.com/element/featured-product/',
                            'docs_url' => 'https://master-addons.com/docs/addons/featured-product-element/',
                            'tuts_url' => 'https://youtu.be/nh6NVP5IQf0',
                            'tuts_features' => [
                                "Visually highlight a single product",
                                "Great for affiliate offer layouts",
                                "Custom image, title, and CTA",
                                "Attractive and flexible design options"
                            ],
                            'is_pro'   => true,
                        ],
                        // 'ma-product-review' => [
                        //     'title'    => 'Product Review',
                        //     'icon'     => 'eicon-columns',
                        //     'class'    => 'MasterAddons\Addons\Product_Review',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],
                        // 'ma-wc-product-carousel' => [
                        //     'title'    => 'WC Product Carousel',
                        //     'icon'     => 'eicon-slideshow',
                        //     'class'    => 'MasterAddons\Addons\WC_Product_Carousel',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],
                        // 'ma-wc-product-table' => [
                        //     'title'    => 'WC Product Table',
                        //     'icon'     => 'eicon-woocommerce',
                        //     'class'    => 'MasterAddons\Addons\WC_Product_Table',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],
                        // 'ma-wc-products-gallery' => [
                        //     'title'    => 'WC Products Gallery',
                        //     'icon'     => 'eicon-gallery-grid',
                        //     'class'    => 'MasterAddons\Addons\WC_Products_Gallery',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],
                        // 'ma-wc-single-product' => [
                        //     'title'    => 'WC Single Product',
                        //     'icon'     => 'eicon-product-info',
                        //     'class'    => 'MasterAddons\Addons\WC_Single_Product',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],
                        // 'ma-wc-add-to-cart' => [
                        //     'title'    => 'WC Add to Cart',
                        //     'icon'     => 'eicon-cart',
                        //     'class'    => 'MasterAddons\Addons\WC_Add_To_Cart',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],
                        // 'woocommerce-products-display' => [
                        //     'title'    => 'WC Products',
                        //     'icon'     => 'eicon-woocommerce',
                        //     'class'    => 'MasterAddons\Addons\Woocommerce_Products_Display',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false,
                        //     'assets'   => [
                        //         'css' => true,
                        //     ],
                        // ],
                        // 'woocommerce-products-carousel-layouts' => [
                        //     'title'    => 'WC Products Carousel',
                        //     'icon'     => 'eicon-woocommerce',
                        //     'class'    => 'MasterAddons\Addons\Woocommerce_Products_Carousel_Display',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false,
                        //     'assets'   => [
                        //         'css' => true,
                        //     ],
                        // ],
                        // 'woocommerce-products-gallery' => [
                        //     'title'    => 'WC Products Gallery',
                        //     'icon'     => 'eicon-woocommerce',
                        //     'class'    => 'MasterAddons\Addons\WooCommerce_Products_Gallery',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false,
                        //     'assets'   => [
                        //         'css' => true,
                        //     ],
                        // ],
                        // 'woocommerce-header-products' => [
                        //     'title'    => 'WC Header Products',
                        //     'icon'     => 'eicon-woocommerce',
                        //     'class'    => 'MasterAddons\Addons\WooCommerce_Header_Products',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false,
                        //     'assets'   => [
                        //         'css' => true,
                        //     ],
                        // ],
                        // 'woocommerce-products-showcase' => [
                        //     'title'    => 'WC Products Showcase',
                        //     'icon'     => 'eicon-woocommerce',
                        //     'class'    => 'MasterAddons\Addons\WooCommerce_Products_Showcase',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false,
                        //     'assets'   => [
                        //         'css' => true,
                        //     ],
                        // ],
                        // 'woocommerce-products-tab' => [
                        //     'title'    => 'WC Products Tab',
                        //     'icon'     => 'eicon-woocommerce',
                        //     'class'    => 'MasterAddons\Addons\WooCommerce_Products_Tab',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false,
                        //     'assets'   => [
                        //         'css' => true,
                        //     ],
                        // ],
                        // 'woocommerce-sale-products-display' => [
                        //     'title'    => 'WC Sale Products',
                        //     'icon'     => 'eicon-woocommerce',
                        //     'class'    => 'MasterAddons\Addons\Woocommerce_Sale_Products_Display',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false,
                        //     'assets'   => [
                        //         'css' => true,
                        //     ],
                        // ],
                        // 'woocommerce-sale-products-carousel-layouts' => [
                        //     'title'    => 'WC Sale Products Carousel',
                        //     'icon'     => 'eicon-woocommerce',
                        //     'class'    => 'MasterAddons\Addons\Woocommerce_Sale_Products_Carousel_Display',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false,
                        //     'assets'   => [
                        //         'css' => true,
                        //     ],
                        // ],
                    ]
                ],
            ],

            /**
             * Extension Groups
             */
            'extensions_category' => [
                'animation' => [
                    'title' => 'Animation',
                    'icon'  => 'eicon-animation',
                    'order' => 10,
                    'extensions' => [
                        'transition' => [
                            'title'    => 'Entrance Animation',
                            'icon'     => 'eicon-animation',
                            'class'    => 'MasterAddons\Pro\Modules\Animation\Transition',
                            'demo_url' => 'https://master-addons.com/extension/entrance-animation/',
                            'docs_url' => 'https://master-addons.com/docs/extensions/entrance-animation/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=kphJEszEAFQ',
                            'tuts_features' => [
                                "Extended entrance animation library",
                                "Custom duration, delay, and repeat",
                                "Triggers on scroll into viewport",
                                "Dozens of animation style options"
                            ],
                            'is_pro'   => true,
                            'ribbons' => [ 'hot' ]
                        ],
                        'transforms' => [
                            'title'    => 'Transforms',
                            'icon'     => 'eicon-sync',
                            'class'    => 'MasterAddons\Pro\Modules\Animation\Transforms',
                            'demo_url' => 'https://master-addons.com/extension/transforms/',
                            'docs_url' => 'https://master-addons.com/docs/extensions/transforms-extension/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=sH2BQT0xOnY',
                            'tuts_features' => [
                                "CSS transform animations on elements",
                                "Move, rotate, scale, and skew",
                                "Trigger on load or interaction",
                                "Per-device transform control support"
                            ],
                            'is_pro'   => true,
                            'ribbons' => [ 'hot' ]
                        ],
                        'reveal' => [
                            'title'    => 'Reveal',
                            'icon'     => 'eicon-animation-text',
                            'class'    => 'MasterAddons\Pro\Modules\Animation\Reveal',
                            'demo_url' => 'https://master-addons.com/extension/reveal/',
                            'docs_url' => 'https://master-addons.com/docs/extensions/reveal-extension/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=xEG1fi_lY1M',
                            'tuts_features' => [
                                "Directional reveal animation on scroll",
                                "Custom speed, delay, and direction",
                                "Custom background color for reveal",
                                "Works on any Elementor element"
                            ],
                            'is_pro'   => true,
                            'ribbons' => [ 'hot' ]
                        ],
                        'rellax' => [
                            'title'    => 'Rellax',
                            'icon'     => 'eicon-scroll',
                            'class'    => 'MasterAddons\Pro\Modules\Animation\Rellax',
                            'demo_url' => 'https://master-addons.com/extension/rellax/',
                            'docs_url' => 'https://master-addons.com/docs/extensions/rellax-extension/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=xYvMVXoZ_NE',
                            'tuts_features' => [
                                "Smooth parallax scrolling effect",
                                "Custom speed and layer depth",
                                "Per-device parallax control",
                                "Adds depth and motion to layouts"
                            ],
                            'is_pro'   => true,
                        ],
                        'floating-effects' => [
                            'title'    => 'Floating Effects',
                            'icon'     => 'eicon-parallax',
                            'class'    => 'MasterAddons\Pro\Modules\Animation\FloatingEffects',
                            'demo_url' => 'https://master-addons.com/extension/floating-effects/',
                            'docs_url' => 'https://master-addons.com/docs/extensions/floating-effects/',
                            'tuts_url' => 'https://youtu.be/GG-hl3cG3Dw',
                            'tuts_features' => [
                                "Floating and bobbing animations",
                                "Rotating effects on any element",
                                "Custom speed and direction control",
                                "Makes elements visually stand out"
                            ],
                            'is_pro'   => true,
                            'ribbons' => [ 'popular' ]
                        ],
                        'particles' => [
                            'title'    => 'Particles',
                            'icon'     => 'eicon-background',
                            'class'    => 'MasterAddons\Modules\Animation\Particles',
                            'demo_url' => 'https://master-addons.com/extension/particles/',
                            'docs_url' => 'https://master-addons.com/docs/extensions/particles-extension/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=sNC0pik_g3Q',
                            'tuts_features' => [
                                "Animated particle effects on sections",
                                "Polygons, snow, and stars options",
                                "Custom particle count and speed",
                                "Interactive mouse hover movement"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'js'      => true,
                                'vendors' => ['particles'],
                            ],
                        ],
                        'animated-gradient' => [
                            'title'    => 'Animated Gradient BG',
                            'icon'     => 'eicon-barcode',
                            'class'    => 'MasterAddons\Pro\Modules\Animation\AnimatedGradient',
                            'demo_url' => 'https://master-addons.com/extension/animated-gradient-bg/',
                            'docs_url' => 'https://master-addons.com/docs/extensions/animated-gradient-background-extension/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=jANMWGDaeG0',
                            'tuts_features' => [
                                "Smoothly transitioning animated gradients",
                                "Apply to sections and columns",
                                "Custom color stops and speed",
                                "No coding required to configure"
                            ],
                            'is_pro'   => true,
                            'assets'   => [
                                'js' => true,
                            ],
                        ],
                        'bg-slider' => [
                            'title'    => 'Background Slider',
                            'icon'     => 'eicon-slider-push',
                            'class'    => 'MasterAddons\Modules\Animation\BgSlider',
                            'demo_url' => 'https://master-addons.com/extension/background-slider/',
                            'docs_url' => 'https://master-addons.com/docs/extensions/background-slider-extension/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=Z6ujz7Hunjg',
                            'tuts_features' => [
                                "Multi-image background slideshow",
                                "Ken Burns zoom and pan effect",
                                "Custom transition speed and style",
                                "Works on any section or container"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'js'      => true,
                                'vendors' => ['vegas'],
                            ],
                        ],
                        // 'morphing-effects' => [
                        //     'title'    => 'Morphing Effects',
                        //     'icon'     => 'eicon-parallax',
                        //     'class'    => 'MasterAddons\Pro\Modules\Animation\MorphingEffects',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],
                        // 'parallax-background' => [
                        //     'title'    => 'Parallax Background',
                        //     'icon'     => 'eicon-parallax',
                        //     'class'    => 'MasterAddons\Pro\Modules\Animation\ParallaxBackground',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],
                        // 'parallax-elements' => [
                        //     'title'    => 'Parallax Elements',
                        //     'icon'     => 'eicon-parallax',
                        //     'class'    => 'MasterAddons\Pro\Modules\Animation\ParallaxElements',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        // ],
                    ],
                ],
                'display' => [
                    'title' => 'Display',
                    'icon'  => 'eicon-preview-medium',
                    'order' => 20,
                    'extensions' => [
                        'glassmorphism' => [
                            'title'    => 'Glassmorphism',
                            'icon'     => 'eicon-adjust',
                            'class'    => 'MasterAddons\Modules\Display\Glassmorphism',
                            'demo_url' => 'https://master-addons.com/extension/glassmorphism/',
                            'docs_url' => 'https://master-addons.com/docs/extensions/elementor-glassmorphism-effects/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=P_2IZVslLlE',
                            'tuts_features' => [
                                "Frosted glass effect on sections",
                                "Background blur and opacity control",
                                "Modern layered design aesthetic",
                                "Custom border and shadow styling"
                            ],
                            'is_pro'   => false,
                            'ribbons' => [ 'popular' ]
                        ],
                        'patterns' => [
                            'title'    => 'Patterns',
                            'icon'     => 'eicon-global-colors',
                            'class'    => 'MasterAddons\Modules\Display\Patterns',
                            'demo_url' => 'https://master-addons.com/extension/patterns/',
                            'docs_url' => 'https://master-addons.com/docs/extensions/pattern/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=AKXqXttG5zI',
                            'tuts_features' => [
                                "Decorative pattern overlays on sections",
                                "Multiple pattern style options",
                                "Custom color and opacity control",
                                "Works on any section background"
                            ],
                            'is_pro'   => false,
                        ],
                        'grid-line' => [
                            'title'    => 'Grid Line',
                            'icon'     => 'eicon-inner-section',
                            'class'    => 'MasterAddons\Pro\Modules\Display\GridLine',
                            'demo_url' => 'https://master-addons.com/extension/grid-line/',
                            'docs_url' => 'https://master-addons.com/docs/extensions/grid-line/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=LsY14ldpXXI',
                            'tuts_features' => [
                                "Decorative grid lines on sections",
                                "Custom line color and opacity",
                                "Horizontal and vertical line options",
                                "Adds structured visual depth effect"
                            ],
                            'is_pro'   => true,
                            'ribbons' => [ 'popular' ]
                        ],
                        'positioning' => [
                            'title'    => 'Positioning',
                            'icon'     => 'eicon-column',
                            'class'    => 'MasterAddons\Modules\Display\Positioning',
                            'demo_url' => 'https://master-addons.com/extension/positioning/',
                            'docs_url' => 'https://master-addons.com/docs/extensions/positioning-extension/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=sXPZv3zVlmY',
                            'tuts_features' => [
                                "Absolute, relative, or fixed positioning",
                                "Per-device positioning controls",
                                "Custom top, left, right, bottom offset",
                                "Works on any Elementor element"
                            ],
                            'is_pro'   => false,
                        ],
                        'extras' => [
                            'title'    => 'Container Extras',
                            'icon'     => 'eicon-plus-square',
                            'class'    => 'MasterAddons\Modules\Display\Extras',
                            'demo_url' => 'https://master-addons.com/extension/container-extras/',
                            'docs_url' => 'https://master-addons.com/docs/extensions/container-extras/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=-_ZsQFq9Z-k',
                            'tuts_features' => [
                                "Precise px or % size control",
                                "Beyond Elementor built-in limits",
                                "Custom min and max width support",
                                "Works on sections and columns"
                            ],
                            'is_pro'   => false,
                        ],
                        'mega-menu' => [
                            'title'    => 'Mega Menu',
                            'icon'     => 'eicon-nav-menu',
                            'class'    => 'MasterAddons\Modules\Display\MegaMenu',
                            'demo_url' => 'https://master-addons.com/extension/mega-menu/',
                            'docs_url' => 'https://master-addons.com/docs/addons/navigation-menu/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=HIf0ud-5Wpo',
                            'tuts_features' => [
                                "Feature-rich mega menu builder",
                                "Use any Elementor widget inside",
                                "3000+ icon support in menus",
                                "Full color and typography controls"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'css' => ['megamenu'],
                                'js'  => ['mega-script'],
                            ],
                            'ribbons' => [ 'featured' ]
                        ],
                        // 'image-masking' => [
                        //     'title'    => 'Image Masking',
                        //     'icon'     => 'eicon-image-rollover',
                        //     'class'    => 'MasterAddons\Modules\Display\ImageMasking',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false,
                        //     'assets'   => [],
                        // ],
                        // 'shape-divider' => [
                        //     'title'    => 'Shape Divider',
                        //     'icon'     => 'eicon-divider-shape',
                        //     'class'    => 'MasterAddons\Modules\Display\ShapeDivider',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false,
                        //     'assets'   => [],
                        // ],
                    ],
                ],
                'dynamic' => [
                    'title' => 'Dynamic',
                    'icon'  => 'eicon-database',
                    'order' => 30,
                    'extensions' => [
                        'dynamic-tags' => [
                            'title'    => 'Dynamic Tags',
                            'icon'     => 'eicon-database',
                            'class'    => 'MasterAddons\Modules\Dynamic\DynamicTags',
                            'demo_url' => 'https://master-addons.com/extension/dynamic-tags/',
                            'docs_url' => 'https://master-addons.com/docs/extensions/dynamic-tags/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=89-whEg85uE',
                            'tuts_features' => [
                                "Support all Elements",
                                "Archive support",
                                "ACF support (coming soon)",
                                "Taxonomy & Meta support"
                            ],
                            'is_pro'   => false,
                            'ribbons' => [ 'featured' ]
                        ],
                        // 'acf-dynamic-tags' => [
                        //     'title'       => 'ACF Dynamic Tags',
                        //     'icon'        => 'eicon-database',
                        //     'class'       => 'MasterAddons\Pro\Modules\Dynamic\AcfDynamicTags',
                        //     'group'       => 'dynamic',
                        //     'subcategory' => 'tags',
                        //     'demo_url'    => 'https://master-addons.com/demos/acf-dynamic-tags/',
                        //     'docs_url'    => 'https://master-addons.com/docs/addons/acf-dynamic-tags/',
                        //     'tuts_url'    => '',
                        //     'is_pro'      => true,
                        // ],
                        'tooltips' => [
                            'title'    => 'Tooltips',
                            'icon'     => 'eicon-info-circle',
                            'class'    => 'MasterAddons\Pro\Modules\Dynamic\Tooltips',
                            'demo_url' => 'https://master-addons.com/extension/tooltips/',
                            'docs_url' => 'https://master-addons.com/docs/extensions/tooltip-extension-for-elementor/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=x-9tBsm6yCg',
                            'tuts_features' => [
                                "Add tooltips to any widget",
                                "Custom content, position, and style",
                                "Works on sections and columns",
                                "Hover and click trigger options"
                            ],
                            'is_pro'   => true,
                            'ribbons' => [ 'popular' ]
                        ],
                        'wrapper-link' => [
                            'title'    => 'Wrapper Link',
                            'icon'     => 'eicon-editor-external-link',
                            'class'    => 'MasterAddons\Modules\Dynamic\WrapperLink',
                            'demo_url' => 'https://master-addons.com/extension/wrapper-link/',
                            'docs_url' => 'https://master-addons.com/docs/extensions/wrapper-link/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=fsbK4G9T-qM',
                            'tuts_features' => [
                                "Make any section fully clickable",
                                "Single URL for entire container",
                                "Works on sections, columns, containers",
                                "No extra markup or coding needed"
                            ],
                            'is_pro'   => false,
                            'assets'   => [
                                'js' => true,
                            ],
                            'ribbons' => [ 'popular' ]
                        ],
                        'display-conditions' => [
                            'title'    => 'Display Conditions',
                            'icon'     => 'eicon-flow',
                            'class'    => 'MasterAddons\Pro\Modules\Dynamic\DisplayConditions',
                            'demo_url' => 'https://master-addons.com/extension/display-conditions/',
                            'docs_url' => 'https://master-addons.com/docs/extensions/display-conditions/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=6hDqKVQmsr8',
                            'tuts_features' => [
                                "Show or hide sections by user role",
                                "Target by browser, OS, or URL",
                                "Login status and date conditions",
                                "Per-element visibility rule control"
                            ],
                            'is_pro'   => true,
                            'ribbons' => [ 'featured' ]
                        ],
                    ],
                ],
                'utilities' => [
                    'title' => 'Utilities',
                    'icon'  => 'eicon-tools',
                    'order' => 40,
                    'extensions' => [
                        'reading-progress-bar' => [
                            'title'    => 'Reading Progress Bar',
                            'icon'     => 'eicon-skill-bar',
                            'class'    => 'MasterAddons\Modules\Utilities\ReadingProgressBar',
                            'demo_url' => 'https://master-addons.com/extension/reading-progress-bar/',
                            'docs_url' => 'https://master-addons.com/docs/extensions/reading-progress-bar/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=eJIDMioxbZI',
                            'tuts_features' => [
                                "Reading progress indicator for posts",
                                "Configurable color and height",
                                "Ideal for long-form blog content",
                                "Fixed position at top of page"
                            ],
                            'is_pro'   => false,
                        ],
                        'custom-css' => [
                            'title'    => 'Custom CSS',
                            'icon'     => 'eicon-code',
                            'class'    => 'MasterAddons\Pro\Modules\Utilities\CustomCss',
                            'demo_url' => 'https://master-addons.com/extension/custom-css/',
                            'docs_url' => 'https://master-addons.com/docs/extensions/custom-css-extension/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=ajXVVGJZuuM',
                            'tuts_features' => [
                                "Write scoped CSS on any element",
                                "Applied only to that element",
                                "No global stylesheet conflicts",
                                "Supports all valid CSS properties"
                            ],
                            'is_pro'   => true,
                            'ribbons' => [ 'popular' ]
                        ],
                        'custom-js' => [
                            'title'    => 'Custom JS',
                            'icon'     => 'eicon-code-bold',
                            'class'    => 'MasterAddons\Pro\Modules\Utilities\CustomJs',
                            'demo_url' => 'https://master-addons.com/extension/custom-js/',
                            'docs_url' => 'https://master-addons.com/docs/extensions/custom-js/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=8G4JLw0s8sI',
                            'tuts_features' => [
                                "Add scoped JavaScript to any element",
                                "Advanced functionality and interactivity",
                                "Runs only on the target element",
                                "No extra plugin or coding setup"
                            ],
                            'is_pro'   => true,
                        ],
                        'duplicator' => [
                            'title'    => 'Post/Page Duplicator',
                            'icon'     => 'eicon-clone',
                            'class'    => 'MasterAddons\Modules\Utilities\Duplicator',
                            'demo_url' => 'https://master-addons.com/extension/post-page-duplicator/',
                            'docs_url' => 'https://master-addons.com/docs/extensions/post-page-duplicator/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=12xY7wMrzHM',
                            'tuts_features' => [
                                "Clone any post or page instantly",
                                "Includes full metadata duplication",
                                "Works with WooCommerce products",
                                "One-click duplicate from dashboard"
                            ],
                            'is_pro'   => false,
                            'ribbons' => [ 'popular' ]
                        ],
                        'which-element' => [
                            'title'    => 'Which Element',
                            'icon'     => 'eicon-handle',
                            'class'    => 'MasterAddons\Modules\Utilities\WhichElement',
                            'demo_url' => 'https://master-addons.com/extension/which-element/',
                            'docs_url' => 'https://master-addons.com/docs/extensions/which-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=LMjHXR0FzaY',
                            'tuts_features' => [
                                "Identify any element on the page",
                                "Quickly inspect widget and settings",
                                "Useful for debugging layouts fast",
                                "Click-to-identify element tool"
                            ],
                            'is_pro'   => false,
                            'ribbons' => [ 'featured' ]
                        ],
                        'magic-copy' => [
                            'title'    => 'Magic Copy',
                            'icon'     => 'eicon-clone',
                            'class'    => 'MasterAddons\Pro\Modules\Utilities\MagicCopy',
                            'demo_url' => 'https://master-addons.com/extension/magic-copy/',
                            'docs_url' => 'https://master-addons.com/docs/extensions/magic-copy/',
                            'tuts_url' => '',
                            'tuts_features' => [
                                "Copy any element across two sites",
                                "Carries source global colors and fonts",
                                "Imports media into the destination",
                                "Does not change destination globals"
                            ],
                            'is_pro'   => true,
                        ],
                        // 'image-optimizer' => [
                        //     'title'    => 'Image Optimizer',
                        //     'icon'     => 'eicon-image-rollover',
                        //     'class'    => 'MasterAddons\Pro\Admin\Image_Optimizer\Image_Optimizer',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        //     'ribbons' => [ 'upcoming' ]
                        // ],
                        // 'cloudflare-turnstile' => [
                        //     'title'    => 'Cloudflare Turnstile',
                        //     'icon'     => 'eicon-lock-user',
                        //     'class'    => 'MasterAddons\Pro\Modules\Utilities\CloudflareTurnstile',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true,
                        //     'ribbons' => [ 'upcoming' ]
                        // ],
                    ]
                ],
            ],

            'icons_extended' => [
                'title'    => 'Icons Extended',
                'icon'     => 'eicon-favorite',
                'class'    => 'MasterAddons\Modules\Utilities\IconsExtended',
                'is_pro'   => false,
                'libraries' => [
                    'simple-line-icons' => [
                        'title'  => 'Simple Line Icons',
                        'icon'  => 'icon-fire', 
                        'is_pro' => false,
                        'demo_url' => 'https://master-addons.com/icon-library/',
                        'docs_url' => 'https://master-addons.com/docs/extensions/icon-library/',
                        'tuts_url' => 'https://www.youtube.com/watch?v=sp3XSiB_Uu8',
                        'tuts_features' => [
                            "5 Free Icon library",
                            "3 Pro icon library",
                            "Lightweight icons",
                            "Faster performance on loading"
                        ],
                    ],
                    'elementor-icons' => [
                        'title'  => 'Elementor Icons',
                        'icon'  => 'eicon-elementor',
                        'is_pro' => false,
                        'demo_url' => 'https://master-addons.com/icon-library/',
                        'docs_url' => 'https://master-addons.com/docs/extensions/icon-library/',
                        'tuts_url' => 'https://www.youtube.com/watch?v=sp3XSiB_Uu8',
                        'tuts_features' => [
                            "5 Free Icon library",
                            "3 Pro icon library",
                            "Lightweight icons",
                            "Faster performance on loading"
                        ],
                    ],
                    'iconic-fonts' => [
                        'title'  => 'Ionic Font',
                        'is_pro' => false,
                        'demo_url' => 'https://master-addons.com/icon-library/',
                        'docs_url' => 'https://master-addons.com/docs/extensions/icon-library/',
                        'tuts_url' => 'https://www.youtube.com/watch?v=sp3XSiB_Uu8',
                        'tuts_features' => [
                            "5 Free Icon library",
                            "3 Pro icon library",
                            "Lightweight icons",
                            "Faster performance on loading"
                        ],
                    ],
                    'linear-icons' => [
                        'title'  => 'Linear Icons',
                        'is_pro' => false,
                        'demo_url' => 'https://master-addons.com/icon-library/',
                        'docs_url' => 'https://master-addons.com/docs/extensions/icon-library/',
                        'tuts_url' => 'https://www.youtube.com/watch?v=sp3XSiB_Uu8',
                        'tuts_features' => [
                            "5 Free Icon library",
                            "3 Pro icon library",
                            "Lightweight icons",
                            "Faster performance on loading"
                        ],
                    ],
                    'material-icons' => [
                        'title'  => 'Material Icons',
                        'is_pro' => false,
                        'demo_url' => 'https://master-addons.com/icon-library/',
                        'docs_url' => 'https://master-addons.com/docs/extensions/icon-library/',
                        'tuts_url' => 'https://www.youtube.com/watch?v=sp3XSiB_Uu8',
                        'tuts_features' => [
                            "5 Free Icon library",
                            "3 Pro icon library",
                            "Lightweight icons",
                            "Faster performance on loading"
                        ],
                    ],
                    'feather-icons' => [
                        'title'  => 'Feather Icons',
                        'is_pro' => true,
                        'demo_url' => 'https://master-addons.com/icon-library/',
                        'docs_url' => 'https://master-addons.com/docs/extensions/icon-library/',
                        'tuts_url' => 'https://www.youtube.com/watch?v=sp3XSiB_Uu8',
                        'tuts_features' => [
                            "5 Free Icon library",
                            "3 Pro icon library",
                            "Lightweight icons",
                            "Faster performance on loading"
                        ],
                    ],
                    'remix-icons' => [
                        'title'  => 'Remix Icons',
                        'is_pro' => true,
                        'demo_url' => 'https://master-addons.com/icon-library/',
                        'docs_url' => 'https://master-addons.com/docs/extensions/icon-library/',
                        'tuts_url' => 'https://www.youtube.com/watch?v=sp3XSiB_Uu8',
                        'tuts_features' => [
                            "5 Free Icon library",
                            "3 Pro icon library",
                            "Lightweight icons",
                            "Faster performance on loading"
                        ],
                    ],
                    'teeny-icons' => [
                        'title'  => 'Teeny Icons',
                        'is_pro' => true,
                        'demo_url' => 'https://master-addons.com/icon-library/',
                        'docs_url' => 'https://master-addons.com/docs/extensions/icon-library/',
                        'tuts_url' => 'https://www.youtube.com/watch?v=sp3XSiB_Uu8',
                        'tuts_features' => [
                            "5 Free Icon library",
                            "3 Pro icon library",
                            "Lightweight icons",
                            "Faster performance on loading"
                        ],
                    ],
                ],
            ],

            /**
             * Third-Party Plugin Integrations
             */
            'integrations' => [
                'custom-breakpoints' => [
                    'title'       => 'Custom Breakpoints',
                    'class'       => 'MasterCustomBreakPoint\JLTMA_Master_Custom_Breakpoint',
                    'wp_slug'     => 'custom-breakpoints-for-elementor',
                    'plugin_file' => 'custom-breakpoints-for-elementor/custom-breakpoints-for-elementor.php',
                ],
                'adminify' => [
                    'title'       => 'WP Adminify',
                    'class'       => 'WPAdminify\WP_Adminify',
                    'wp_slug'     => 'adminify',
                    'plugin_file' => 'adminify/adminify.php',
                ],
            ],
        ];

        /**
         * Filter: Allow pro and third-party to modify config
         *
         * @param array $config Complete configuration array
         */
        self::$config = apply_filters('jltma_config', self::$config);

        return self::$config;
    }

    /**
     * Get all addons (widgets) - flattened for backward compatibility
     * Structure: addons_category > group > {title, icon, order, addons: widgets}
     *
     * @return array Flat array of all addons with group info added
     */
    public static function get_addons()
    {
        $config = self::get_config();
        $categories = $config['addons_category'] ?? [];
        $flat_addons = [];

        foreach ($categories as $group => $group_data) {
            if (!is_array($group_data) || !isset($group_data['addons'])) {
                continue;
            }

            $addons = $group_data['addons'];
            foreach ($addons as $key => $addon) {
                if (!is_array($addon)) {
                    continue;
                }
                $addon['group'] = $group;
                $flat_addons[$key] = $addon;
            }
        }

        return $flat_addons;
    }

    /**
     * Get addons organized by groups (new structure)
     *
     * @return array Grouped addons array (group > {title, icon, order, addons})
     */
    public static function get_addons_grouped()
    {
        $config = self::get_config();
        return $config['addons_category'] ?? [];
    }

    /**
     * Get addons by group
     *
     * @param string $group Group key (basic, content, media, forms, etc.)
     * @return array Flat array of addons in this group
     */
    public static function get_addons_by_group($group)
    {
        $config = self::get_config();
        $group_data = $config['addons_category'][$group] ?? [];

        if (empty($group_data) || !isset($group_data['addons'])) {
            return [];
        }

        $flat_addons = [];
        foreach ($group_data['addons'] as $key => $addon) {
            if (is_array($addon)) {
                $addon['group'] = $group;
                $flat_addons[$key] = $addon;
            }
        }

        return $flat_addons;
    }

    /**
     * Get all extensions - flattened for backward compatibility
     * Structure: extensions_category > group > {title, icon, order, extensions: items}
     *
     * @return array Flat array of all extensions with group info added
     */
    public static function get_extensions()
    {
        $config = self::get_config();
        $categories = $config['extensions_category'] ?? [];
        $flat_extensions = [];

        foreach ($categories as $group => $group_data) {
            if (!is_array($group_data) || !isset($group_data['extensions'])) {
                continue;
            }

            $extensions = $group_data['extensions'];
            foreach ($extensions as $key => $extension) {
                if (!is_array($extension)) {
                    continue;
                }
                $extension['group'] = $group;
                $flat_extensions[$key] = $extension;
            }
        }

        return $flat_extensions;
    }

    /**
     * Get extensions organized by groups
     *
     * @return array Grouped extensions array (group > {title, icon, order, extensions})
     */
    public static function get_extensions_grouped()
    {
        $config = self::get_config();
        return $config['extensions_category'] ?? [];
    }

    /**
     * Get extensions by group
     *
     * @param string $group Group key (animation, display, dynamic, utilities)
     * @return array Flat array of extensions in this group
     */
    public static function get_extensions_by_group($group)
    {
        $config = self::get_config();
        $group_data = $config['extensions_category'][$group] ?? [];

        if (empty($group_data) || !isset($group_data['extensions'])) {
            return [];
        }

        $flat_extensions = [];
        foreach ($group_data['extensions'] as $key => $extension) {
            if (is_array($extension)) {
                $extension['group'] = $group;
                $flat_extensions[$key] = $extension;
            }
        }

        return $flat_extensions;
    }

    /**
     * Get all icon libraries
     *
     * @return array
     */
    public static function get_icons()
    {
        $config = self::get_config();
        return $config['icons_extended']['libraries'] ?? [];
    }

    /**
     * Get all recommended plugins / integrations
     *
     * @return array
     */
    public static function get_plugins()
    {
        $config = self::get_config();
        return $config['integrations'] ?? [];
    }

    /**
     * Get UI groups configuration
     *
     * @return array
     */
    public static function get_groups()
    {
        $config = self::get_config();
        return $config['groups'] ?? [];
    }

    /**
     * Get a single addon by key
     *
     * Supports legacy prefixes by normalizing to 'ma-' prefix:
     * - 'ma-el-' → 'ma-'
     * - 'jltma-' → 'ma-'
     *
     * @param string $key Addon key (e.g., 'ma-accordion', 'ma-el-countdown-timer', 'jltma-blog')
     * @return array|null
     */
    public static function get_addon($key)
    {
        $addons = self::get_addons();

        // Try exact match first
        if (isset($addons[$key])) {
            return $addons[$key];
        }

        // Normalize legacy 'ma-el-' prefix to 'ma-'
        if (strpos($key, 'ma-el-') === 0) {
            $normalized_key = 'ma-' . substr($key, 6); // Remove 'ma-el-' and add 'ma-'
            if (isset($addons[$normalized_key])) {
                return $addons[$normalized_key];
            }
        }

        // Normalize 'jltma-' prefix to 'ma-'
        if (strpos($key, 'jltma-') === 0) {
            $normalized_key = 'ma-' . substr($key, 6); // Remove 'jltma-' and add 'ma-'
            if (isset($addons[$normalized_key])) {
                return $addons[$normalized_key];
            }
        }

        return null;
    }

    /**
     * Get addon config by class name
     *
     * Looks up addon configuration using the widget's class name.
     * Returns both the config key and the addon data.
     *
     * @param string $class_name Full class name (e.g., 'MasterAddons\Addons\Counter_Up')
     * @return array|null Array with 'key' and 'config', or null if not found
     */
    public static function get_addon_by_class($class_name)
    {
        $addons = self::get_addons();

        foreach ($addons as $key => $addon) {
            if (isset($addon['class']) && $addon['class'] === $class_name) {
                return [
                    'key'    => $key,
                    'config' => $addon,
                ];
            }
        }

        return null;
    }

    /**
     * Get a single extension by key
     *
     * @param string $key Extension key
     * @return array|null
     */
    public static function get_extension($key)
    {
        $extensions = self::get_extensions();
        return $extensions[$key] ?? null;
    }

    /**
     * Get addon/extension docs (demo_url, docs_url, tuts_url)
     *
     * @param string $key Addon or extension key
     * @return array
     */
    public static function get_addon_docs($key)
    {
        // Try addons first, then extensions
        $config = self::get_addon($key) ?? self::get_extension($key);

        return [
            'demo_url' => $config['demo_url'] ?? '',
            'docs_url' => $config['docs_url'] ?? '',
            'tuts_url' => $config['tuts_url'] ?? '',
        ];
    }

    /**
     * Get addon assets with fallback
     *
     * @param string $key Addon key
     * @return array
     */
    public static function get_addon_assets($key)
    {
        $addon = self::get_addon($key);

        if (!$addon) {
            // Fallback: derive from key
            $css_name = str_replace('ma-', '', $key);
            return [
                'css'     => [$css_name],
                'js'      => [],
                'vendors' => [],
            ];
        }

        return wp_parse_args($addon['assets'] ?? [], [
            'css'     => [],
            'js'      => [],
            'vendors' => [],
        ]);
    }

    /**
     * Check if an addon/extension/icon is pro
     *
     * @param string $key Item key
     * @return bool
     */
    public static function is_pro($key)
    {
        // Check addons
        $addon = self::get_addon($key);
        if ($addon) {
            return !empty($addon['is_pro']);
        }

        // Check extensions
        $extensions = self::get_extensions();
        if (isset($extensions[$key])) {
            return !empty($extensions[$key]['is_pro']);
        }

        // Check icons
        $icons = self::get_icons();
        if (isset($icons[$key])) {
            return !empty($icons[$key]['is_pro']);
        }

        return false;
    }

    /**
     * Get all items (addons + extensions) for settings page
     * Returns in old format for backward compatibility
     *
     * @return array
     */
    public static function get_all_elements_for_settings()
    {
        $addons = self::get_addons();
        $result = [];

        foreach ($addons as $key => $addon) {
            $result[] = [
                'key'         => $key,
                'title'       => $addon['title'] ?? '',
                'class'       => $addon['class'] ?? '',
                'demo_url'    => $addon['demo_url'] ?? '',
                'docs_url'    => $addon['docs_url'] ?? '',
                'tuts_url'    => $addon['tuts_url'] ?? '',
                'is_pro'      => $addon['is_pro'] ?? false,
                'group'       => $addon['group'] ?? '',
                'subcategory' => $addon['subcategory'] ?? '',
            ];
        }

        return $result;
    }

    /**
     * Get all extensions for settings page
     * Returns in old format for backward compatibility
     *
     * @return array
     */
    public static function get_all_extensions_for_settings()
    {
        $extensions = self::get_extensions();
        $result = [];

        foreach ($extensions as $key => $extension) {
            $result[] = [
                'key'      => $key,
                'title'    => $extension['title'],
                'class'    => $extension['class'] ?? '',
                'demo_url' => $extension['demo_url'] ?? '',
                'docs_url' => $extension['docs_url'] ?? '',
                'tuts_url' => $extension['tuts_url'] ?? '',
                'is_pro'   => $extension['is_pro'] ?? false,
            ];
        }

        return $result;
    }

    /**
     * Get subcategories for a group
     *
     * @param string $group Group key
     * @return array
     */
    public static function get_subcategories($group)
    {
        $config = self::get_config();
        return $config['groups'][$group]['subcategories'] ?? [];
    }

    /**
     * Get extension groups configuration
     *
     * @return array
     */
    public static function get_extension_groups()
    {
        $config = self::get_config();
        return $config['extension_groups'] ?? [];
    }

    /**
     * Get addons by group and subcategory - directly from nested structure
     * For groups without subcategories, returns all addons when subcategory matches group name
     *
     * @param string $group Group key
     * @param string $subcategory Subcategory key
     * @return array
     */
    public static function get_addons_by_subcategory($group, $subcategory)
    {
        $config = self::get_config();
        $group_data = $config['addons'][$group] ?? [];

        if (empty($group_data)) {
            return [];
        }

        // Check if this group has direct widgets (no subcategories)
        $first_item = reset($group_data);
        $has_direct_widgets = is_array($first_item) && isset($first_item['title']);

        if ($has_direct_widgets) {
            // Group has widgets directly - return all widgets if subcategory matches group
            return ($subcategory === $group) ? $group_data : [];
        }

        return $group_data[$subcategory] ?? [];
    }

    /**
     * Get addons grouped by subcategory within a group
     * Returns the nested structure with subcategory metadata
     * For groups without subcategories, returns a single virtual subcategory
     *
     * @param string $group Group key
     * @return array Addons organized by subcategory with titles and order
     */
    public static function get_addons_grouped_by_subcategory($group)
    {
        $config = self::get_config();
        $group_addons = $config['addons'][$group] ?? [];
        $result = [];

        if (empty($group_addons)) {
            return $result;
        }

        // Check if this group has direct widgets (no subcategories)
        $first_item = reset($group_addons);
        $has_direct_widgets = is_array($first_item) && isset($first_item['title']);

        if ($has_direct_widgets) {
            // Group has widgets directly - create a single virtual subcategory
            $group_info = self::get_groups()[$group] ?? ['title' => ucfirst($group)];
            $result[$group] = [
                'title'  => $group_info['title'] ?? ucfirst($group),
                'order'  => 10,
                'addons' => $group_addons,
            ];
        } else {
            // Group has subcategories
            $subcategories = self::get_subcategories($group);
            foreach ($group_addons as $subcategory => $addons) {
                $subcat_info = $subcategories[$subcategory] ?? ['title' => ucfirst($subcategory), 'order' => 999];
                $result[$subcategory] = [
                    'title'  => $subcat_info['title'],
                    'order'  => $subcat_info['order'] ?? 999,
                    'addons' => $addons,
                ];
            }
        }

        // Sort by order
        uasort($result, function ($a, $b) {
            return ($a['order'] ?? 999) <=> ($b['order'] ?? 999);
        });

        return $result;
    }

    /**
     * Get extensions by group and subcategory - directly from nested structure
     *
     * @param string $group Group key
     * @param string $subcategory Subcategory key
     * @return array
     */
    public static function get_extensions_by_subcategory($group, $subcategory)
    {
        $config = self::get_config();
        return $config['extensions'][$group][$subcategory] ?? [];
    }

    /**
     * Get extensions grouped by subcategory within a group
     *
     * @param string $group Group key
     * @return array Extensions organized by subcategory
     */
    public static function get_extensions_grouped_by_subcategory($group)
    {
        $config = self::get_config();
        $group_extensions = $config['extensions'][$group] ?? [];
        $extension_groups = self::get_extension_groups();
        $subcategories = $extension_groups[$group]['subcategories'] ?? [];
        $result = [];

        // Build result with subcategory metadata
        foreach ($group_extensions as $subcategory => $extensions) {
            $subcat_info = $subcategories[$subcategory] ?? ['title' => ucfirst($subcategory), 'order' => 999];
            $result[$subcategory] = [
                'title'      => $subcat_info['title'],
                'order'      => $subcat_info['order'] ?? 999,
                'extensions' => $extensions,
            ];
        }

        // Sort by order
        uasort($result, function ($a, $b) {
            return ($a['order'] ?? 999) <=> ($b['order'] ?? 999);
        });

        return $result;
    }

    /**
     * Get addon icon by key
     *
     * @param string $key Addon key (e.g., 'ma-accordion')
     * @return string Icon class (without jltma-icon prefix)
     */
    public static function get_addon_icon($key)
    {
        $addon = self::get_addon($key);
        if ($addon && !empty($addon['icon'])) {
            return $addon['icon'];
        }

        // Fallback icons based on addon type
        $fallbacks = [
            'ma-' => 'eicon-apps',
        ];

        foreach ($fallbacks as $prefix => $icon) {
            if (strpos($key, $prefix) === 0) {
                return $icon;
            }
        }

        return 'eicon-apps';
    }

    /**
     * Get extension icon by key
     *
     * @param string $key Extension key
     * @return string Icon class (without jltma-icon prefix)
     */
    public static function get_extension_icon($key)
    {
        $extension = self::get_extension($key);
        if ($extension && !empty($extension['icon'])) {
            return $extension['icon'];
        }

        return 'eicon-settings';
    }

    /**
     * Singleton
     */
    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
