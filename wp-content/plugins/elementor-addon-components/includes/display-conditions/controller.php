<?php
/**
 * Class: Controller
 *
 * Description:
 *
 * @since 2.1.7
 */

namespace EACCustomWidgets\Includes\DisplayConditions;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Element_Base;

class Controller {

	private const COND_DIR       = __DIR__ . '/conditions/';
	private const COND_NAMESPACE = __NAMESPACE__ . '\\Conditions\\';

	/**
	 * La liste des classes des fichiers du rep. conditions
	 *
	 * @access private
	 * @var array conditions_file_classes.
	 */
	private $conditions_file_classes = array();

	/**
	 * Le préfix des controls
	 *
	 * @access private
	 * @var string element_condition_.
	 */
	private $prefix_condition = 'element_condition_';

	/**
	 * $tags_list
	 *
	 * Liste des classes: Nom du fichier PHP => class
	 */
	private $file_class_list = array(
		'day-week'       => 'Day_Week',
		'date-compare'   => 'Date_Compare',
		'date-range'     => 'Date_Range',
		'logged-in-user' => 'Logged_In_User',
		'user-role'      => 'User_Role',
		'user-lang'      => 'User_Lang',
		'post'           => 'Post',
		'page'           => 'Page',
		'page-static'    => 'Page_Static',
		'post-type'      => 'Post_Type',
		'post-author'    => 'Post_Author',
		'post-category'  => 'Post_Category',
		'post-tag'       => 'Post_Tag',
	);

	/** Constructeur */
	public function __construct() {
		include_once __DIR__ . '/conditions/condition-base.php';

		$this->active_conditions_classes();

		if ( ! \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$eac_has_filter = has_filter( 'elementor/frontend/widget/should_render', array( __CLASS__, 'should_render' ) );
			add_filter( 'elementor/frontend/widget/should_render', array( $this, 'should_render' ), 10, 2 );
			add_filter( 'elementor/frontend/column/should_render', array( $this, 'should_render' ), 10, 2 );
			add_filter( 'elementor/frontend/section/should_render', array( $this, 'should_render' ), 10, 2 );
			add_filter( 'elementor/frontend/container/should_render', array( $this, 'should_render' ), 10, 2 );
		}
	}

	/**
	 * get_conditions_list
	 *
	 * Retourne la liste des conditions
	 *
	 * @access public
	 */
	public function get_conditions_list(): array {
		return array(
			esc_html__( 'Time', 'eac-components' )    => array(
				'label'   => esc_html__( 'Date', 'eac-components' ),
				'options' => array(
					'day_week'     => esc_html__( 'Day of the week', 'eac-components' ),
					'date_compare' => esc_html__( 'Today', 'eac-components' ),
					'date_range'   => esc_html__( 'Today (range)', 'eac-components' ),
				),
			),
			esc_html__( 'Visitor', 'eac-components' ) => array(
				'label'   => esc_html__( 'Visitor', 'eac-components' ),
				'options' => array(
					'logged_in_user' => esc_html__( 'Not logged-in', 'eac-components' ),
					'user_role'      => esc_html__( 'Role', 'eac-components' ),
					'user_lang'      => esc_html__( 'Language', 'eac-components' ),
				),
			),
			esc_html__( 'Post', 'eac-components' )  => array(
				'label'   => esc_html__( 'Post', 'eac-components' ),
				'options' => array(
					'post'          => esc_html__( 'Post', 'eac-components' ),
					'page'          => esc_html__( 'Page', 'eac-components' ),
					'page_static'   => esc_html__( 'Static page', 'eac-components' ),
					'post_type'     => esc_html__( 'Post type', 'eac-components' ),
					'post_author'   => esc_html__( 'Post author', 'eac-components' ),
					'post_category' => esc_html__( 'Post category', 'eac-components' ),
					'post_tag'      => esc_html__( 'Post tag', 'eac-components' ),
				),
			),
		);
	}

	/**
	 * get_conditions_flat_list
	 *
	 * Retourne la liste des conditions sous forme de tableau plat
	 *
	 * @access public
	 */
	public function get_conditions_flat_list(): array {
		return array(
			'day_week'       => esc_html__( 'Day of the week', 'eac-components' ),
			'date_compare'   => esc_html__( 'Today', 'eac-components' ),
			'date_range'     => esc_html__( 'Today (range)', 'eac-components' ),
			'logged_in_user' => esc_html__( 'Not logged-in', 'eac-components' ),
			'user_role'      => esc_html__( 'Role', 'eac-components' ),
			'user_lang'      => esc_html__( 'Language', 'eac-components' ),
			'post'           => esc_html__( 'Post', 'eac-components' ),
			'page'           => esc_html__( 'Page', 'eac-components' ),
			'page_static'    => esc_html__( 'Static page', 'eac-components' ),
			'post_type'      => esc_html__( 'Post type', 'eac-components' ),
			'post_author'    => esc_html__( 'Post author', 'eac-components' ),
			'post_category'  => esc_html__( 'Post category', 'eac-components' ),
			'post_tag'       => esc_html__( 'Post tag', 'eac-components' ),
		);
	}

	/**
	 * active_conditions_classes
	 *
	 * Charge les classes des conditions répertoire Conditions
	 *
	 * @access public
	 */
	public function active_conditions_classes(): void {
		foreach ( $this->file_class_list as $file => $class_name ) {
			$full_class_name = self::COND_NAMESPACE . $class_name;
			$file_path       = self::COND_DIR . $file . '.php';

			if ( ! file_exists( $file_path ) ) {
				continue;
			}
			$file = str_replace( '-', '_', $file );
			$this->conditions_file_classes[ $file ] = new $full_class_name();
		}
	}

	/**
	 * add_controls_to_compare
	 * Ajout des controls de comparaison à chaque control condition
	 *
	 * @param \Elementor\Repeater $element
	 *
	 * @return void
	 */
	public function add_controls_to_compare( $element ): void {
		foreach ( $this->conditions_file_classes as $condition_file_name => $condition_class ) {
			$control_id      = $this->prefix_condition . $condition_file_name;
			$content_control = $condition_class->get_target_control(); // Ajout des controls enregistrés dans les classes conditions

			if ( ! empty( $content_control ) ) {
				$element->add_control(
					$control_id,
					$content_control
				);
			}
		}
	}

	/**
	 * should_render
	 *
	 * Évalue les conditions d'affichage selon critères
	 *
	 * @param Boolean      $should_render Le boolean
	 * @param Element_Base $element  L'élément en cours d'édition
	 * @return boolean     true ou false
	 */
	public function should_render( $should_render, $element ): bool {
		$settings = $element->get_settings_for_display();

		if ( isset( $settings['element_condition_active'] ) && 'yes' === $settings['element_condition_active'] ) {
			$operateur           = '';
			$should_render_array = array();
			$condition_list      = $settings['element_condition_list'];
			$condition_when      = $settings['element_condition_when'];

			/** Boucle sur les items du repeater */
			foreach ( $condition_list as $index => $condition ) {
				$control_id = $this->prefix_condition . $condition['element_condition_key'];

				if ( isset( $condition[ $control_id ] ) && ! empty( $condition[ $control_id ] ) && isset( $this->conditions_file_classes[ $condition['element_condition_key'] ] ) ) {
					if ( 'date_compare' === $condition['element_condition_key'] ) {
						$operateur = $condition['element_condition_operateur_date'];
					} elseif ( ! in_array( $condition['element_condition_key'], array( 'date_compare', 'logged_in_user', 'page_static' ), true ) ) {
						$operateur = $condition['element_condition_operateur_range'];
					}

					$control_class = $this->conditions_file_classes[ $condition['element_condition_key'] ];
					$date_du_jour  = $this->get_today_date( 'server' );

					// On check les conditions
					array_push( $should_render_array, (bool) $control_class->check( $settings, $condition[ $control_id ], $operateur, $date_du_jour ) );
				}
			}

			if ( ! empty( $should_render_array ) && 'all' === $condition_when ) {
				$result = count( array_unique( $should_render_array ) );
				if ( 1 === $result ) {
					$should_render = $should_render_array[0];
				}
			} elseif ( ! empty( $should_render_array ) && 'any' === $condition_when ) {
				$result = count( array_unique( $should_render_array ) );
				if ( 2 === $result ) {
					$should_render = false;
				} else {
					$should_render = $should_render_array[0];
				}
			}

			if ( false === $should_render && 'yes' === $settings['element_condition_fallback_active'] ) {
				$content = $settings['element_condition_fallback_content'];
				$class   = 'element-condition_fallback-' . $element->get_id();
				$id      = 'element-condition_labelled-' . $element->get_id();
				?>
					<div class='<?php echo esc_attr( $class ); ?>' role='status' aria-labelledby='<?php echo esc_attr( $id ); ?>' tabindex='0'>
						<div id='<?php echo esc_attr( $id ); ?>'><?php echo nl2br( wp_kses_post( $content ) ); ?></div>
					</div>
				<?php
			}
		}

		return $should_render;
	}

	/**
	 * get_today_date
	 *
	 * Calcule la date du jour du serveur ou du client (browser)
	 *
	 * @param String $tz 'server' ou 'client'
	 * @return String La date du jour au format (Y-m-d) ou une chaine vide
	 */
	public function get_today_date( $tz = '' ): string {
		$current_timezone = wp_timezone_string(); // Timezone du serveur par défaut
		$offset           = 0;

		/**
		 * Pour la timezone du client, on recherche son adresse IP
		 * On interroge le service 'geoplugin'
		 * Et on récupère la timezone du client
		 */
		if ( 'client' === $tz ) {
			$ip = '';

			if ( empty( $ip ) ) {
				if ( isset( $_SERVER['REMOTE_ADDR'] ) && filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP ) ) {
					$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
				}
			}

			if ( empty( $ip ) || '127.0.0.1' === $ip || 'localhost' === $ip ) {
				return '';
			}

			$get_geoplugin = wp_safe_remote_get( "http://www.geoplugin.net/json.gp?ip=$ip" );

			if ( ! is_wp_error( $get_geoplugin ) && ! empty( wp_remote_retrieve_body( $get_geoplugin ) ) ) {
				$ip_geo = json_decode( wp_remote_retrieve_body( $get_geoplugin ), true );

				if ( 200 === $ip_geo['geoplugin_status'] ) {
					$current_timezone = isset( $ip_geo['geoplugin_timezone'] ) ? $ip_geo['geoplugin_timezone'] : $current_timezone;
				}
			}
		}

		$this_tz = new \DateTimeZone( $current_timezone );
		// Date heure de Greenwich
		$now_utc = new \DateTime( 'now', new \DateTimeZone( 'UTC' ) );
		// nombre d'heures de décallage entre la timezone et Greenwich
		$offset = $this_tz->getOffset( $now_utc ) / 3600;
		// Ajoute à Greenwich le décallage
		$today = $now_utc->add( \DateInterval::createFromDateString( "{$offset} hours" ) );

		/**$city = isset( $ip_geo ) && ! empty( $ip_geo['geoplugin_city'] ) ? $ip_geo['geoplugin_city'] : '';
		var_dump( $city . '::' . $current_timezone . '::' . $offset . '::' . $today->format( 'Y-m-d g:i a' ) . '::' . $today->format( 'Y-m-d' ) );*/

		return $today->format( 'Y-m-d' );
	}
}
