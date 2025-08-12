<?php

namespace MasterAddons\Admin\Dashboard\Addons\Elements;

if (!class_exists('JLTMA_Addon_Elements')) {
    class JLTMA_Addon_Elements
    {
        private static $instance = null;
        public static $jltma_elements;

        public function __construct()
        {
            self::$jltma_elements = [
                'jltma-addons'      => [
                    // 'title'             => esc_html__('Content Elements', 'master-addons'),
                    'title'             => 'Content Elements',
                    'elements'          => [
                        [
                            // 'title'    => esc_html__('Animated Headlines', 'master-addons'),
                            'title'    => 'Animated Headlines',
                            'key'      => 'ma-animated-headlines',
                            'class'    => 'MasterAddons\Addons\JLTMA_Animated_Headlines',
                            'demo_url' => 'https://master-addons.com/demos/animated-headline/',
                            'docs_url' => 'https://master-addons.com/docs/addons/animated-headline-elementor/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=09QIUPdUQnM',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Call to Action', 'master-addons'),
                            'title'    => 'Call to Action',
                            'key'      => 'ma-call-to-action',
                            'class'    => 'MasterAddons\Addons\JLTMA_Call_to_Action',
                            'demo_url' => 'https://master-addons.com/demos/call-to-action/',
                            'docs_url' => 'https://master-addons.com/docs/addons/call-to-action/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=iY2q1jtSV5o',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Dual Heading', 'master-addons'),
                            'title'    => 'Dual Heading',
                            'key'      => 'ma-dual-heading',
                            'class'    => 'MasterAddons\Addons\JLTMA_Dual_Heading',
                            'demo_url' => 'https://master-addons.com/demos/dual-heading/',
                            'docs_url' => 'https://master-addons.com/docs/addons/dual-heading/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=kXyvNe6l0Sg',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Advanced Accordion', 'master-addons'),
                            'title'    => 'Advanced Accordion',
                            'key'      => 'ma-accordion',
                            'class'    => 'MasterAddons\Addons\JLTMA_Advanced_Accordion',
                            'demo_url' => 'https://master-addons.com/demos/advanced-accordion/',
                            'docs_url' => 'https://master-addons.com/docs/addons/elementor-accordion-widget/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=rdrqWa-tf6Q',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Tabs', 'master-addons'),
                            'title'    => 'Tabs',
                            'key'      => 'ma-tabs',
                            'class'    => 'MasterAddons\Addons\JLTMA_Tabs',
                            'demo_url' => 'https://master-addons.com/demos/tabs/',
                            'docs_url' => 'https://master-addons.com/docs/addons/tabs-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=lsqGmIrdahw',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Tooltip', 'master-addons'),
                            'title'    => 'Tooltip',
                            'key'      => 'ma-tooltip',
                            'class'    => 'MasterAddons\Addons\JLTMA_Tooltip',
                            'demo_url' => 'https://master-addons.com/demos/tooltip/',
                            'docs_url' => 'https://master-addons.com/docs/addons/adding-tooltip-in-elementor-editor/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=Av3eTae9vaE',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Progress Bar', 'master-addons'),
                            'title'    => 'Progress Bar',
                            'key'      => 'ma-progressbar',
                            'class'    => 'MasterAddons\Addons\JLTMA_Progress_Bar',
                            'demo_url' => 'https://master-addons.com/demos/progress-bar/',
                            'docs_url' => 'https://master-addons.com/docs/addons/how-to-create-and-customize-progressbar-in-elementor/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=77-b1moRE8M',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Progress Bars', 'master-addons'),
                            'title'    => 'Progress Bars',
                            'key'      => 'ma-progressbars',
                            'class'    => 'MasterAddons\Addons\JLTMA_Progress_Bars',
                            'demo_url' => 'https://master-addons.com/demos/multiple-progress-bars/',
                            'docs_url' => 'https://master-addons.com/docs/addons/progress-bars-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=Mc9uDWJQMIY',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Team Member', 'master-addons'),
                            'title'    => 'Team Member',
                            'key'      => 'ma-team-members',
                            'class'    => 'MasterAddons\Addons\JLTMA_Team_Member',
                            'demo_url' => 'https://master-addons.com/demos/team-member/',
                            'docs_url' => 'https://master-addons.com/docs/addons/adding-team-members-in-elementor-page-builder/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=wXPEl93_UBw',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Team Slider', 'master-addons'),
                            'title'    => 'Team Slider',
                            'key'      => 'ma-team-members-slider',
                            'class'    => 'MasterAddons\Addons\JLTMA_Team_Slider',
                            'demo_url' => 'https://master-addons.com/demos/team-carousel/',
                            'docs_url' => 'https://master-addons.com/docs/addons/team-members-carousel/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=ubP_h86bP-c',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Creative Button', 'master-addons'),
                            'title'    => 'Creative Button',
                            'key'      => 'ma-creative-buttons',
                            'class'    => 'MasterAddons\Addons\JLTMA_Creative_Button',
                            'demo_url' => 'https://master-addons.com/demos/creative-button/',
                            'docs_url' => 'https://master-addons.com/docs/addons/creative-button/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=kFq8l6wp1iI',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Changelogs', 'master-addons'),
                            'title'    => 'Changelogs',
                            'key'      => 'ma-changelog',
                            'class'    => 'MasterAddons\Addons\JLTMA_Changelogs',
                            'demo_url' => 'https://master-addons.com/changelogs/',
                            'docs_url' => 'https://master-addons.com/docs/addons/changelog-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=qWRgJkFfBow',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Infobox', 'master-addons'),
                            'title'    => 'Infobox',
                            'key'      => 'ma-infobox',
                            'class'    => 'MasterAddons\Addons\JLTMA_Infobox',
                            'demo_url' => 'https://master-addons.com/demos/infobox/',
                            'docs_url' => 'https://master-addons.com/docs/addons/infobox-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=2-ymXAZfrF0',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Flipbox', 'master-addons'),
                            'title'    => 'Flipbox',
                            'key'      => 'ma-flipbox',
                            'class'    => 'MasterAddons\Addons\JLTMA_Flipbox',
                            'demo_url' => 'https://master-addons.com/demos/flipbox/',
                            'docs_url' => 'https://master-addons.com/docs/addons/how-to-configure-flipbox-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=f-B35-xWqF0',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Creative Links', 'master-addons'),
                            'title'    => 'Creative Links',
                            'key'      => 'ma-creative-links',
                            'class'    => 'MasterAddons\Addons\JLTMA_Creative_Links',
                            'demo_url' => 'https://master-addons.com/demos/creative-link/',
                            'docs_url' => 'https://master-addons.com/docs/addons/how-to-add-creative-links/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=o6SmdwMJPyA',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Image Hover Effects', 'master-addons'),
                            'title'    => 'Image Hover Effects',
                            'key'      => 'ma-image-hover-effects',
                            'class'    => 'MasterAddons\Addons\JLTMA_Image_Hover_Effects',
                            'demo_url' => 'https://master-addons.com/demos/image-hover-effects/',
                            'docs_url' => 'https://master-addons.com/docs/addons/image-hover-effects-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=vWGWzuRKIss',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Blog', 'master-addons'),
                            'title'    => 'Blog',
                            'key'      => 'ma-blog',
                            'class'    => 'MasterAddons\Addons\JLTMA_Blog',
                            'demo_url' => 'https://master-addons.com/demos/blog-element/',
                            'docs_url' => 'https://master-addons.com/docs/addons/blog-element-customization/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=03AcgVEsTaA',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('News Ticker', 'master-addons'),
                            'title'    => 'News Ticker',
                            'key'      => 'ma-news-ticker',
                            'class'    => 'MasterAddons\Addons\JLTMA_News_Ticker',
                            'demo_url' => 'https://master-addons.com/demos/news-ticker/',
                            'docs_url' => 'https://master-addons.com/docs/addons/news-ticker-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=jkrBCzebQ-E',
                            'is_pro'   => true
                        ],
                        [
                            // 'title'    => esc_html__('Timeline', 'master-addons'),
                            'title'    => 'Timeline',
                            'key'      => 'ma-timeline',
                            'class'    => 'MasterAddons\Addons\JLTMA_Timeline',
                            'demo_url' => 'https://master-addons.com/demos/timeline/',
                            'docs_url' => 'https://master-addons.com/docs/addons/timeline-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=0mcDMKrH1A0',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Business Hours', 'master-addons'),
                            'title'    => 'Business Hours',
                            'key'      => 'ma-business-hours',
                            'class'    => 'MasterAddons\Addons\JLTMA_Business_Hours',
                            'demo_url' => 'https://master-addons.com/demos/business-hours/',
                            'docs_url' => 'https://master-addons.com/docs/addons/business-hours-elementor/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=x0_HY9uYgog',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Table of Contents', 'master-addons'),
                            'title'    => 'Table of Contents',
                            'key'      => 'ma-table-of-contents',
                            'class'    => 'MasterAddons\Addons\JLTMA_Table_of_Contents',
                            'demo_url' => 'https://master-addons.com/100-best-elementor-addons/',
                            'docs_url' => '',
                            'tuts_url' => '',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Image Hotspot', 'master-addons'),
                            'title'    => 'Image Hotspot',
                            'key'      => 'ma-image-hotspot',
                            'class'    => 'MasterAddons\Addons\JLTMA_Image_Hotspot',
                            'demo_url' => 'https://master-addons.com/demos/image-hotspot/',
                            'docs_url' => 'https://master-addons.com/docs/addons/image-hotspot/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=IDAd_d986Hg',
                            'is_pro'   => true
                        ],
                        [
                            // 'title'    => esc_html__('Filterable Image Gallery', 'master-addons'),
                            'title'    => 'Filterable Image Gallery',
                            'key'      => 'ma-image-filter-gallery',
                            'class'    => 'MasterAddons\Addons\JLTMA_Filterable_Image_Gallery',
                            'demo_url' => 'https://master-addons.com/demos/image-gallery/',
                            'docs_url' => 'https://master-addons.com/docs/addons/filterable-image-gallery/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=h7egsnX4Ewc',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Pricing Table', 'master-addons'),
                            'title'    => 'Pricing Table',
                            'key'      => 'ma-pricing-table',
                            'class'    => 'MasterAddons\Addons\JLTMA_Pricing_Table',
                            'demo_url' => 'https://master-addons.com/demos/pricing-table/',
                            'docs_url' => 'https://master-addons.com/docs/addons/pricing-table-elementor-free-widget/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=_FUk1EfLBUs',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Image Comparison', 'master-addons'),
                            'title'    => 'Image Comparison',
                            'key'      => 'ma-image-comparison',
                            'class'    => 'MasterAddons\Addons\JLTMA_Image_Comparison',
                            'demo_url' => 'https://master-addons.com/demos/image-comparison/',
                            'docs_url' => 'https://master-addons.com/docs/addons/image-comparison-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=3nqRRXSGk3M',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Restrict Content', 'master-addons'),
                            'title'    => 'Restrict Content',
                            'key'      => 'ma-restrict-content',
                            'class'    => 'MasterAddons\Addons\JLTMA_Restrict_Content',
                            'demo_url' => 'https://master-addons.com/demos/restrict-content-for-elementor/',
                            'docs_url' => 'https://master-addons.com/docs/addons/restrict-content-for-elementor/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=Alc1R_W5_Z8',
                            'is_pro'   => true
                        ],
                        [
                            // 'title'    => esc_html__('Current Time', 'master-addons'),
                            'title'    => 'Current Time',
                            'key'      => 'ma-current-time',
                            'class'    => 'MasterAddons\Addons\JLTMA_Current_Time',
                            'demo_url' => 'https://master-addons.com/demos/current-time/',
                            'docs_url' => 'https://master-addons.com/docs/addons/current-time/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=Icwi5ynmzkQ',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Domain Search', 'master-addons'),
                            'title'    => 'Domain Search',
                            'key'      => 'ma-domain-checker',
                            'class'    => 'MasterAddons\Addons\JLTMA_Domain_Search',
                            'demo_url' => 'https://master-addons.com/demos/domain-search/',
                            'docs_url' => 'https://master-addons.com/docs/addons/how-ma-domain-checker-works/',
                            'tuts_url' => '',
                            'is_pro'   => true
                        ],
                        [
                            // 'title'    => esc_html__('Dynamic Table', 'master-addons'),
                            'title'    => 'Dynamic Table',
                            'key'      => 'ma-table',
                            'class'    => 'MasterAddons\Addons\JLTMA_Dynamic_Table',
                            'demo_url' => 'https://master-addons.com/demos/dynamic-table/',
                            'docs_url' => 'https://master-addons.com/docs/addons/dynamic-table-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=bn0TvaGf9l8',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Nav Menu', 'master-addons'),
                            'title'    => 'Nav Menu',
                            'key'      => 'ma-navmenu',
                            'class'    => 'MasterAddons\Addons\JLTMA_Nav_Menu',
                            'demo_url' => 'https://master-addons.com/elementor-mega-menu/',
                            'docs_url' => 'https://master-addons.com/docs/addons/navigation-menu/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=WhA5YnE4yJg',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Search', 'master-addons'),
                            'title'    => 'Search',
                            'key'      => 'ma-search',
                            'class'    => 'MasterAddons\Addons\JLTMA_Search',
                            'demo_url' => 'https://master-addons.com/demos/search-element/',
                            'docs_url' => 'https://master-addons.com/docs/addons/search-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=Uk6nnoN5AJ4',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Blockquote', 'master-addons'),
                            'title'    => 'Blockquote',
                            'key'      => 'ma-blockquote',
                            'class'    => 'MasterAddons\Addons\JLTMA_Blockquote',
                            'demo_url' => 'https://master-addons.com/demos/blockquote-element/',
                            'docs_url' => 'https://master-addons.com/docs/addons/blockquote-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=sSCULgPFSHU',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Counter Up', 'master-addons'),
                            'title'    => 'Counter Up',
                            'key'      => 'ma-counter-up',
                            'class'    => 'MasterAddons\Addons\JLTMA_Counter_Up',
                            'demo_url' => 'https://master-addons.com/demos/counter-up/',
                            'docs_url' => 'https://master-addons.com/docs/addons/counter-up-for-elementor/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=9amvO6p9kpM',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Countdown Timer', 'master-addons'),
                            'title'    => 'Countdown Timer',
                            'key'      => 'ma-countdown-timer',
                            'class'    => 'MasterAddons\Addons\JLTMA_Countdown_Timer',
                            'demo_url' => 'https://master-addons.com/demos/countdown-timer/',
                            'docs_url' => 'https://master-addons.com/docs/addons/count-down-timer/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=1lIbOLM9C1I',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Toggle Content', 'master-addons'),
                            'title'    => 'Toggle Content',
                            'key'      => 'ma-toggle-content',
                            'class'    => 'MasterAddons\Addons\JLTMA_Toggle_Content',
                            'demo_url' => 'https://master-addons.com/demos/toggle-content/',
                            'docs_url' => 'https://master-addons.com/docs/addons/toggle-content/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=TH6wbVuWdTA',
                            'is_pro'   => true
                        ],
                        [
                            // 'title'    => esc_html__('Gallery Slider', 'master-addons'),
                            'title'    => 'Gallery Slider',
                            'key'      => 'ma-gallery-slider',
                            'class'    => 'MasterAddons\Addons\JLTMA_Gallery_Slider',
                            'demo_url' => 'https://master-addons.com/demos/gallery-slider/',
                            'docs_url' => 'https://master-addons.com/docs/addons/gallery-slider/',
                            'tuts_url' => '',
                            'is_pro'   => true
                        ],
                        [
                            // 'title'    => esc_html__('Gradient Headline', 'master-addons'),
                            'title'    => 'Gradient Headline',
                            'key'      => 'ma-gradient-headline',
                            'class'    => 'MasterAddons\Addons\JLTMA_Gradient_Headline',
                            'demo_url' => 'https://master-addons.com/demos/gradient-headline/',
                            'docs_url' => 'https://master-addons.com/docs/addons/how-to-add-gradient-headline-in-elementor/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=NgayEI4CthU',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Advanced Image', 'master-addons'),
                            'title'    => 'Advanced Image',
                            'key'      => 'ma-advanced-image',
                            'class'    => 'MasterAddons\Addons\JLTMA_Advanced_Image',
                            'demo_url' => 'https://master-addons.com/demos/advanced-image/',
                            'docs_url' => 'https://master-addons.com/docs/addons/advanced-image-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=fhdwiiy7JiE',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Source Code', 'master-addons'),
                            'title'    => 'Source Code',
                            'key'      => 'ma-source-code',
                            'class'    => 'MasterAddons\Addons\JLTMA_Source_Code',
                            'demo_url' => 'https://master-addons.com/demos/source-code/',
                            'docs_url' => 'https://master-addons.com/docs/addons/source-code-element/',
                            'tuts_url' => 'https://www.youtube.com/watch?v=Yz4m3FI_ccc',
                            'is_pro'   => true
                        ],
                        [
                            // 'title'    => esc_html__('Image Carousel', 'master-addons'),
                            'title'    => 'Image Carousel',
                            'key'      => 'ma-image-carousel',
                            'class'    => 'MasterAddons\Addons\JLTMA_Image_Carousel',
                            'demo_url' => '',
                            'docs_url' => '',
                            'tuts_url' => '',
                            'is_pro'   => false
                        ],
                        [
                            // 'title'    => esc_html__('Logo Slider', 'master-addons'),
                            'title'    => 'Logo Slider',
                            'key'      => 'ma-logo-slider',
                            'class'    => 'MasterAddons\Addons\JLTMA_Logo_Slider',
                            'demo_url' => '',
                            'docs_url' => '',
                            'tuts_url' => '',
                            'is_pro'   => false
                        ],
                        // [
                        //     'title'    => esc_html__('Twitter Slider', 'master-addons' ),
                        //     'title'    => 'Twitter Slider',
                        //     'key'      => 'ma-twitter-slider',
                        //     'class'    => 'MasterAddons\Addons\JLTMA_Twitter_Slider',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false
                        // ],
                        // [
                        //     'title'    => esc_html__('Offcanvas Menu', 'master-addons' ),
                        //     'title'    => 'Offcanvas Menu',
                        //     'key'      => 'ma-offcanvas-menu',
                        //     'class'    => 'MasterAddons\Addons\JLTMA_Offcanvas_Menu',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => true
                        // ],

                        // [
                        //     'title'    => esc_html__('Cascading Image', 'master-addons' ),
                        //     'title'    => 'Cascading Image',
                        //     'key'                => 'ma-image-cascading',
                        //     'demo_url'           => '',
                        //     'docs_url'           => '',
                        //     'tuts_url'           => '',
                        // 'is_pro'            => false
                        // ],

                        // [
                        //     'title'    => esc_html__('Morphing Blob', 'master-addons' ),
                        //     'title'    => 'Morphing Blob',
                        //     'key'      => 'ma-morphing-blob',
                        //     'class'    => 'MasterAddons\Addons\JLTMA_Morphing_Blob',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false
                        // ],

                        // [
                        //     'title'    => esc_html__('Link Navigation', 'master-addons' ),
                        //     'title'    => 'Link Navigation',
                        //     'key'      => 'ma-link-navigation',
                        //     'class'    => 'MasterAddons\Addons\JLTMA_Link_Navigation',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false
                        // ],

                        // [
                        //     'title'    => esc_html__('Audio Playlist', 'master-addons' ),
                        //     'title'    => 'Audio Playlist',
                        //     'key'      => 'ma-audio-playlist',
                        //     'class'    => 'MasterAddons\Addons\JLTMA_Audio_Playlist',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false
                        // ],

                        // [
                        //     'title'    => esc_html__('Dual Button', 'master-addons' ),
                        //     'title'    => 'Dual Button',
                        //     'key'      => 'ma-dual-button',
                        //     'class'    => 'MasterAddons\Addons\JLTMA_Dual_Button',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false
                        // ],
                        // [
                        //     'title'    => esc_html__('Social Share', 'master-addons' ),
                        //     'title'    => 'Social Share',
                        //     'key'      => 'ma-social-share',
                        //     'class'    => 'MasterAddons\Addons\JLTMA_Social_Share',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false
                        // ],
                        // [
                        //     'title'    => esc_html__('Data Table', 'master-addons' ),
                        //     'title'    => 'Data Table',
                        //     'key'      => 'ma-data-table',
                        //     'class'    => 'MasterAddons\Addons\JLTMA_Data_Table',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false
                        // ],
                        // [
                        //     'title'    => esc_html__('Dropdown Button', 'master-addons' ),
                        //     'title'    => 'Dropdown Button',
                        //     'key'      => 'ma-dropdown-button',
                        //     'class'    => 'MasterAddons\Addons\JLTMA_Dropdown_Button',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false
                        // ],

                        // [
                        //     'title'    => esc_html__('PDF Viewer', 'master-addons' ),
                        //     'title'    => 'PDF Viewer',
                        //     'key'      => 'ma-pdf-viewer',
                        //     'class'    => 'MasterAddons\Addons\JLTMA_PDF_Viewer',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false
                        // ],

                        // [
                        //     'title'    => esc_html__('Site Logo', 'master-addons' ),
                        //     'title'    => 'Site Logo',
                        //     'key'      => 'ma-site-logo',
                        //     'class'    => 'MasterAddons\Addons\JLTMA_Site_Logo',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false
                        // ],
                        // [
                        //     'title'    => esc_html__('Pie Chart', 'master-addons' ),
                        //     'title'    => 'Pie Chart',
                        //     'key'      => 'ma-piechart',
                        //     'class'    => 'MasterAddons\Addons\JLTMA_Piechart',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false
                        // ],

                        // [
                        //     'title'    => esc_html__('iFrame', 'master-addons' ),
                        //     'title'    => 'iFrame',
                        //     'key'      => 'ma-iframe',
                        //     'class'    => 'MasterAddons\Addons\JLTMA_iFrame',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false
                        // ],




                        // WooCommerce Addons
                        // [
                        //     'title'    => esc_html__('WC Add to Cart', 'master-addons' ),
                        //     'title'    => 'WC Add to Cart',
                        //     'key'      => 'ma-wc-add-to-cart',
                        //     'class'    => 'MasterAddons\Addons\JLTMA_WC_Add_To_Cart',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false
                        // ],
                        // [
                        //     'title'    => esc_html__('WC Product Slider', 'master-addons' ),
                        //     'title'    => 'WC Product Slider',
                        //     'key'      => 'ma-wc-product-carousel',
                        //     'class'    => 'MasterAddons\Addons\JLTMA_WC_Product_Carousel',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false
                        // ],
                        // [
                        //     'title'    => esc_html__('WC Product Gallery', 'master-addons' ),
                        //     'title'    => 'WC Product Gallery',
                        //     'key'      => 'ma-wc-products-gallery',
                        //     'class'    => 'MasterAddons\Addons\JLTMA_WC_Products_Gallery',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false
                        // ],
                        // [
                        //     'title'    => esc_html__('WC Single Product', 'master-addons' ),
                        //     'title'    => 'WC Single Product',
                        //     'key'      => 'ma-wc-single-product',
                        //     'class'    => 'MasterAddons\Addons\JLTMA_WC_Single_Product',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false
                        // ],

                        // [
                        //     'title'    => esc_html__('WC Product Table', 'master-addons' ),
                        //     'title'    => 'WC Product Table',
                        //     'key'      => 'ma-wc-product-table',
                        //     'class'    => 'MasterAddons\Addons\JLTMA_WC_Product_Table',
                        //     'demo_url' => '',
                        //     'docs_url' => '',
                        //     'tuts_url' => '',
                        //     'is_pro'   => false
                        // ],


                    ]
                ]
            ];
        }

        public static function get_instance()
        {
            if (!self::$instance) {
                self::$instance = new self;
            }
            return self::$instance;
        }
    }
    JLTMA_Addon_Elements::get_instance();
}
