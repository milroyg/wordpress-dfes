<?php

namespace MasterAddons\Modules;

use \Elementor\Controls_Manager;

if (!class_exists('MasterAddons\Modules\JLTMA_Extension_Particles')) {
class JLTMA_Extension_Particles
{

  private static $_instance = null;

  public function __construct()
  {

    add_action('elementor/element/after_section_end', [$this, 'register_controls'], 10, 3);

    // Only enqueue scripts when needed (no longer auto-enqueue in editor)
    add_action('elementor/preview/enqueue_scripts', [$this, 'maybe_enqueue_particles_for_preview']);

    // Add print template for editor preview
    add_action('elementor/element/print_template', [$this, '_print_template'], 10, 2);
    add_action('elementor/section/print_template', [$this, '_print_template'], 10, 2);
    add_action('elementor/column/print_template', [$this, '_print_template'], 10, 2);
    add_action('elementor/container/print_template', [$this, '_print_template'], 10, 2);

    add_action('elementor/frontend/element/before_render', [$this, '_before_render'], 10, 1);
    add_action('elementor/frontend/column/before_render', [$this, '_before_render'], 10, 1);
    add_action('elementor/frontend/section/before_render', [$this, '_before_render'], 10, 1);
    add_action('elementor/frontend/container/before_render', [$this, '_before_render'], 10, 1);

    add_action('elementor/frontend/section/after_render', array($this, 'after_render'));
  }

  public function jltma_add_particles_scripts()
  {
    wp_enqueue_script('master-addons-particles');
  }

  public function maybe_enqueue_particles_for_preview()
  {
    // Check if any element on the page has particles enabled
    global $post;
    if (!$post) return;

    // Get Elementor data
    $document = \Elementor\Plugin::$instance->documents->get($post->ID);
    if (!$document) return;

    $data = $document->get_elements_data();
    if ($this->has_particles_enabled($data)) {
      $this->jltma_add_particles_scripts();
    }
  }

  private function has_particles_enabled($elements)
  {
    foreach ($elements as $element) {
      $settings = $element['settings'] ?? [];

      // Check if this element has particles enabled
      if (isset($settings['ma_el_enable_particles']) && $settings['ma_el_enable_particles'] === 'yes') {
        return true;
      }

      // Check child elements recursively
      if (!empty($element['elements'])) {
        if ($this->has_particles_enabled($element['elements'])) {
          return true;
        }
      }
    }
    return false;
  }


  public function register_controls($element, $section_id, $args)
  {

    if (('section' === $element->get_name() && 'section_background' === $section_id) || ('column' === $element->get_name() && 'section_style' === $section_id) || ('container' === $element->get_name() && 'section_background' === $section_id)) {

      $element->start_controls_section(
        'ma_el_particles',
        [
          'tab' => Controls_Manager::TAB_STYLE,
          'label' =>  __('Particles ', 'master-addons' ) . JLTMA_EXTENSION_BADGE
        ]
      );

      $element->add_control(
        'ma_el_particles_apply_changes',
        [
          'type' => Controls_Manager::RAW_HTML,
          'raw' => '<div class="elementor-update-preview-button editor-ma-preview-update"><span>Update changes to Preview</span><button class="elementor-button elementor-button-success" onclick="elementor.reloadPreview();">Apply</button></div>',
          'separator' => 'after'
        ]
      );


      // $element->add_control(
      //     'ma_el_particle_video_tutorial',
      //     [
      //         'raw' => '<br><a href="#" target="_blank">Watch Video Tutorial <span class="dashicons dashicons-video-alt3"></span></a>',
      //         'type' => Controls_Manager::RAW_HTML,
      //     ]
      // );


      $element->add_control(
        'ma_el_enable_particles',
        [
          'type'  => Controls_Manager::SWITCHER,
          'label' => __('Enable Particle Background', 'master-addons' ),
          'default' => '',
          'label_on' => __('Yes', 'master-addons' ),
          'label_off' => __('No', 'master-addons' ),
          'return_value' => 'yes',
          'prefix_class' => 'jltma-particle-',
          'render_type' => 'template',
        ]
      );


      $element->add_control(
        'ma_el_particle_area_zindex',
        [
          'label'              => __('Z-index', 'master-addons' ),
          'type'               => Controls_Manager::NUMBER,
          'default'            => 0,
          'condition'          => [
            'ma_el_enable_particles' => 'yes',
          ],
          'frontend_available' => true,
        ]
      );


      $element->add_control(
        'ma_el_enable_particles_alert',
        [
          'type' => Controls_Manager::RAW_HTML,
          'content_classes' => 'ma_el_enable_particles_alert elementor-control-field-description',
          'raw' => __('<a href="https://vincentgarreau.com/particles.js/" target="_blank">Click here</a> to generate JSON for the below field. </br><a href="https://master-addons.com/add-particles-background-in-elementor-section/" target="_blank">Know more</a> about using this feature.', 'master-addons' ),
          'separator' => 'none',
          'condition' => [
            'ma_el_enable_particles' => 'yes',
          ],
        ]
      );

      $element->add_control(
        'ma_el_particle_json',
        [
          'type'    => Controls_Manager::CODE,
          'label'   => __('Add Particle Json', 'master-addons' ),
          'default' => '{
                                  "particles": {
                                    "number": {
                                      "value": 80,
                                      "density": {
                                        "enable": true,
                                        "value_area": 800
                                      }
                                    },
                                    "color": {
                                      "value": "#ffffff"
                                    },
                                    "shape": {
                                      "type": "circle",
                                      "stroke": {
                                        "width": 0,
                                        "color": "#000000"
                                      },
                                      "polygon": {
                                        "nb_sides": 5
                                      },
                                      "image": {
                                        "src": "img/github.svg",
                                        "width": 100,
                                        "height": 100
                                      }
                                    },
                                    "opacity": {
                                      "value": 0.5,
                                      "random": false,
                                      "anim": {
                                        "enable": false,
                                        "speed": 1,
                                        "opacity_min": 0.1,
                                        "sync": false
                                      }
                                    },
                                    "size": {
                                      "value": 3,
                                      "random": true,
                                      "anim": {
                                        "enable": false,
                                        "speed": 40,
                                        "size_min": 0.1,
                                        "sync": false
                                      }
                                    },
                                    "line_linked": {
                                      "enable": true,
                                      "distance": 150,
                                      "color": "#ffffff",
                                      "opacity": 0.4,
                                      "width": 1
                                    },
                                    "move": {
                                      "enable": true,
                                      "speed": 6,
                                      "direction": "none",
                                      "random": false,
                                      "straight": false,
                                      "out_mode": "out",
                                      "bounce": false,
                                      "attract": {
                                        "enable": false,
                                        "rotateX": 600,
                                        "rotateY": 1200
                                      }
                                    }
                                  },
                                  "interactivity": {
                                    "detect_on": "canvas",
                                    "events": {
                                      "onhover": {
                                        "enable": true,
                                        "mode": "repulse"
                                      },
                                      "onclick": {
                                        "enable": true,
                                        "mode": "push"
                                      },
                                      "resize": true
                                    },
                                    "modes": {
                                      "grab": {
                                        "distance": 400,
                                        "line_linked": {
                                          "opacity": 1
                                        }
                                      },
                                      "bubble": {
                                        "distance": 400,
                                        "size": 40,
                                        "duration": 2,
                                        "opacity": 8,
                                        "speed": 3
                                      },
                                      "repulse": {
                                        "distance": 200,
                                        "duration": 0.4
                                      },
                                      "push": {
                                        "particles_nb": 4
                                      },
                                      "remove": {
                                        "particles_nb": 2
                                      }
                                    }
                                  },
                                  "retina_detect": true
                                }',
          'render_type' => 'template',
          'condition' => [
            'ma_el_enable_particles' => 'yes'
          ]
        ]
      );

      $element->end_controls_section();
    }
  }

  public function _before_render($element)
  {

    if ($element->get_name() != 'section' && $element->get_name() != 'column' && $element->get_name() != 'container') {
      return;
    }

    $settings = $element->get_settings();
    if ($settings['ma_el_enable_particles'] == 'yes') {
      // Clean and validate JSON
      $particle_json = $settings['ma_el_particle_json'];
      $particle_data = json_decode($particle_json, true);

      if ($particle_data) {
        $element->add_render_attribute('_wrapper', 'data-jltma-particle', json_encode($particle_data));
      }
      $element->add_render_attribute('_wrapper', 'data-jltma-particle-zindex', $settings['ma_el_particle_area_zindex']);

      // Only enqueue particles script when particles are enabled
      $this->jltma_add_particles_scripts();
    }
  }


  function _print_template($template, $widget)
  {
    if ($widget->get_name() != 'section' && $widget->get_name() != 'column' && $widget->get_name() != 'container') {
      return $template;
    }

    ob_start();

    echo '<div class="jltma-particle-wrapper" id="jltma-particle-{{ view.getID() }}" data-jltma-particles-editor="{{ settings.ma_el_particle_json }}" data-jltma-particle-zindex="{{ settings.ma_el_particle_area_zindex }}"></div>';

    $particles_content = ob_get_contents();
    ob_end_clean();

    return $template . $particles_content;
  }

  public function after_render($element)
  {

    $data     = $element->get_data();
    $settings = $element->get_settings_for_display();
    $type     = $data['elType'];
    $zindex   = !empty($settings['ma_el_particle_area_zindex']) ? $settings['ma_el_particle_area_zindex'] : 0;
    if (($type === 'section' || $type === 'column' || $type === 'container') && ($element->get_settings('ma_el_enable_particles') === 'yes')) {
    ?>
      <style>
        /* Frontend styles */
        .elementor-element-<?php echo sanitize_text_field($element->get_id()); ?> .jltma-particle-wrapper > canvas {
          z-index: <?php echo esc_attr($zindex); ?>;
          position: absolute;
          top: 0;
          left: 0;
          width: 100% !important;
          height: 100% !important;
          pointer-events: none;
        }
        .elementor-element-<?php echo sanitize_text_field($element->get_id()); ?> .jltma-particle-wrapper {
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          overflow: hidden;
          pointer-events: none;
          z-index: <?php echo esc_attr($zindex); ?>;
        }
        .elementor-element-<?php echo sanitize_text_field($element->get_id()); ?>.jltma-particle-yes {
          position: relative;
          overflow: hidden;
        }

        /* Editor-specific styles */
        .elementor-editor-active .elementor-element-<?php echo sanitize_text_field($element->get_id()); ?>.jltma-particle-yes {
          position: relative !important;
          overflow: hidden !important;
        }
        .elementor-editor-active .elementor-element-<?php echo sanitize_text_field($element->get_id()); ?> .jltma-particle-wrapper {
          position: absolute !important;
          top: 0 !important;
          left: 0 !important;
          width: 100% !important;
          height: 100% !important;
          pointer-events: none !important;
          z-index: <?php echo esc_attr($zindex); ?> !important;
          overflow: hidden !important;
        }
        .elementor-editor-active .elementor-element-<?php echo sanitize_text_field($element->get_id()); ?> .jltma-particle-wrapper > canvas {
          position: absolute !important;
          top: 0 !important;
          left: 0 !important;
          width: 100% !important;
          height: 100% !important;
          pointer-events: none !important;
          z-index: <?php echo esc_attr($zindex); ?> !important;
        }

        /* Container support */
        .elementor-element-<?php echo sanitize_text_field($element->get_id()); ?>.e-con.jltma-particle-yes {
          position: relative;
          overflow: hidden;
        }
      </style>
<?php
    }
  }


  public static function instance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }
}
}

if (class_exists('MasterAddons\Modules\JLTMA_Extension_Particles')) {
  JLTMA_Extension_Particles::instance();
}
