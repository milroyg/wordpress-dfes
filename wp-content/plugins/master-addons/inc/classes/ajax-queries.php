<?php

namespace MasterAddons\Inc\Classes;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

use \Elementor\Plugin;
use \Elementor\Core\Common\Modules\Ajax\Module as Ajax;

use MasterAddons\Master_Elementor_Addons;
use MasterAddons\Inc\Classes\Helper;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 1/7/20
 */

class Ajax_Queries
{

    public $loaded_templates = [];

    private static $instance = null;

    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }


    public function __construct()
    {

        // $this->loaded_templates[] = $this->loaded_templates;

        // Restrict Content
        add_action('wp_ajax_jltma_restrict_content', array($this, 'jltma_restrict_content'));
        add_action('wp_ajax_nopriv_jltma_restrict_content', array($this, 'jltma_restrict_content'));

        // Domain Checker
        add_action('wp_ajax_jltma_domain_checker', array($this, 'jltma_domain_checker'));
        add_action('wp_ajax_nopriv_jltma_domain_checker', array($this, 'jltma_domain_checker'));

        // Elementor Ajax Requests
        add_action('elementor/ajax/register_actions', [$this, 'jltma_register_ajax_actions']);
    }

    public function jltma_register_ajax_actions($ajax_manager)
    {
        $ajax_manager->register_ajax_action('jltma_query_control_value_titles', [$this, 'jltma_ajax_call_control_value_titles']);
        $ajax_manager->register_ajax_action('jltma_query_control_filter_autocomplete', [$this, 'jltma_ajax_call_filter_autocomplete']);
    }


    public function jltma_ajax_call_control_value_titles($request)
    {
        $results = call_user_func([$this, 'get_value_titles_for_' . $request['query_type']], $request);

        return $results;
    }

    // Calls function depending on ajax query data
    public function jltma_ajax_call_filter_autocomplete(array $data)
    {

        if (empty($data['query_type']) || empty($data['q'])) {
            throw new \Exception('Bad Request');
        }

        $results = call_user_func([$this, 'get_autocomplete_for_' . $data['query_type']], $data);

        return [
            'results' => $results,
        ];
    }

    // Gets autocomplete values for 'posts' query type
    protected function get_autocomplete_for_posts($data)
    {
        $results = [];

        $query_params = [
            'post_type'         => sanitize_text_field($data['object_type']),
            's'                 => sanitize_text_field($data['q']),
            'posts_per_page'    => -1,
        ];

        if ('attachment' === $query_params['post_type']) {
            $query_params['post_status'] = 'inherit';
        }

        $query = new \WP_Query($query_params);

        foreach ($query->posts as $post) {
            $results[] = [
                'id'    => $post->ID,
                'text'  => $post->post_title,
            ];
        }

        return $results;
    }

    // Gets autocomplete values for taxonomy 'terms' query type
    protected function get_autocomplete_for_terms($data)
    {
        $results = [];

        $taxonomies = get_object_taxonomies('');

        $query_params = [
            'taxonomy'      => $taxonomies,
            'search'        => sanitize_text_field($data['q']),
            'hide_empty'    => false,
        ];

        $terms = get_terms($query_params);

        foreach ($terms as $term) {
            $taxonomy = get_taxonomy($term->taxonomy);

            $results[] = [
                'id'    => $term->term_id,
                'text'  => $taxonomy->labels->singular_name . ': ' . $term->name,
            ];
        }

        return $results;
    }


    // Gets autocomplete values for 'authors' query type
    protected function get_autocomplete_for_authors($data)
    {
        $results = [];

        $query_params = [
            'who'                   => 'authors',
            'has_published_posts'   => true,
            'fields'                => [
                'ID',
                'display_name',
            ],
            'search'                => '*' . sanitize_text_field($data['q']) . '*',
            'search_columns'        => [
                'user_login',
                'user_nicename',
            ],
        ];

        $user_query = new \WP_User_Query($query_params);

        foreach ($user_query->get_results() as $author) {
            $results[] = [
                'id'    => $author->ID,
                'text'  => $author->display_name,
            ];
        }

        return $results;
    }

    // Gets value titles for 'terms' query type - for saved values
    protected function get_value_titles_for_terms($data)
    {
        $results = [];

        if (empty($data['id'])) {
            return $results;
        }

        $term_ids = is_array($data['id']) ? $data['id'] : array($data['id']);

        foreach ($term_ids as $term_id) {
            $term = get_term($term_id);

            if ($term && !is_wp_error($term)) {
                $taxonomy = get_taxonomy($term->taxonomy);
                $results[ $term_id ] = $taxonomy->labels->singular_name . ': ' . $term->name;
            }
        }

        return $results;
    }

    // Gets value titles for 'posts' query type - for saved values
    protected function get_value_titles_for_posts($data)
    {
        $results = [];

        if (empty($data['id'])) {
            return $results;
        }

        $post_ids = is_array($data['id']) ? $data['id'] : array($data['id']);

        foreach ($post_ids as $post_id) {
            $post = get_post($post_id);

            if ($post) {
                $results[ $post_id ] = $post->post_title;
            }
        }

        return $results;
    }

    // Gets value titles for 'authors' query type - for saved values
    protected function get_value_titles_for_authors($data)
    {
        $results = [];

        if (empty($data['id'])) {
            return $results;
        }

        $author_ids = is_array($data['id']) ? $data['id'] : array($data['id']);

        foreach ($author_ids as $author_id) {
            $author = get_user_by('id', $author_id);

            if ($author) {
                $results[ $author_id ] = $author->display_name;
            }
        }

        return $results;
    }


    // Restrict Content
    public function jltma_restrict_content()
    {
        // Verify nonce first (localized to the frontend script as JLTMA_SCRIPTS.nonce).
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'master-addons-elementor' ) ) {
            wp_send_json_error( __( 'Security check failed.', 'master-addons' ) );
        }

        parse_str( isset( $_POST['fields'] ) ? sanitize_text_field( wp_unslash( $_POST['fields'] ) ) : '', $output ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above

        if (!empty($_POST['fields'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above

            // Math Captcha
            if ( isset( $_POST['restrict_type'] ) && $_POST['restrict_type'] == 'math_captcha' ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- no nonce available for this AJAX handler
                // Field names match the rendered form inputs (jltma_rc_answer / _hd).
                $rc_answer    = isset( $output['jltma_rc_answer'] ) ? trim( $output['jltma_rc_answer'] ) : '';
                $rc_answer_hd = isset( $output['jltma_rc_answer_hd'] ) ? trim( $output['jltma_rc_answer_hd'] ) : '';
                if ( $rc_answer === '' || $rc_answer !== $rc_answer_hd ) {
                    die(json_encode(array(
                        "result" => "validate",
                        "output" => /* translators: %s: Error message */ sprintf(__('%1$s &nbsp;', 'master-addons' ), wp_kses_post( wp_unslash( isset( $_POST['error_message'] ) ? $_POST['error_message'] : '' ) )) // phpcs:ignore WordPress.Security.NonceVerification.Missing -- no nonce available for this AJAX handler
                    )));
                }
            }

            // Password Protection
            if ( isset( $_POST['restrict_type'] ) && $_POST['restrict_type'] == 'password' ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- no nonce available for this AJAX handler
                if ( ( isset( $_POST['content_pass'] ) ? sanitize_text_field( wp_unslash( $_POST['content_pass'] ) ) : '' ) !== $output['ma_el_restrict_content_pass'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- no nonce available for this AJAX handler
                    $error_msg = isset($_POST['error_message']) ? stripslashes(sanitize_text_field( wp_unslash( $_POST['error_message'] ) )) : __('Incorrect password.', 'master-addons'); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- no nonce available for this AJAX handler
                    die(json_encode(array(
                        "result" => "validate",
                        "output" => $error_msg
                    )));
                }
            }


            // Age Restrict
            if ( isset( $_POST['restrict_type'] ) && $_POST['restrict_type'] == 'age_restrict' ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- no nonce available for this AJAX handler

                $min_age = isset( $_POST['restrict_age']['min_age'] ) ? (float) sanitize_text_field( wp_unslash( $_POST['restrict_age']['min_age'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- no nonce available for this AJAX handler

                // Enter Age
                if ( isset( $_POST['restrict_age']['age_type'] ) && sanitize_key( wp_unslash( $_POST['restrict_age']['age_type'] ) ) === "enter_age" ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- no nonce available for this AJAX handler

                    if (($output['ma_el_ra_year'] == "") || ($output['ma_el_ra_year'] < $min_age)) {
                        die(json_encode(array(
                            "result" => "validate",
                            "output" => /* translators: %s: Error message */ sprintf(__('%1$s &nbsp;', 'master-addons' ), wp_kses_post( wp_unslash( isset( $_POST['error_message'] ) ? $_POST['error_message'] : '' ) )) // phpcs:ignore WordPress.Security.NonceVerification.Missing -- no nonce available for this AJAX handler
                        )));
                    }
                }

                if ( isset( $_POST['restrict_age']['age_type'] ) && sanitize_key( wp_unslash( $_POST['restrict_age']['age_type'] ) ) === "age_checkbox" ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- no nonce available for this AJAX handler
                    // Checkbox Age Restriction
                    if ($output['ma_el_rc_check'] != "on") {
                        die(json_encode(array(
                            "result" => "validate",
                            "output" => /* translators: %s: Error message */ sprintf(__('%1$s &nbsp;', 'master-addons' ), wp_kses_post( wp_unslash( isset( $_POST['error_message'] ) ? $_POST['error_message'] : '' ) )) // phpcs:ignore WordPress.Security.NonceVerification.Missing -- no nonce available for this AJAX handler
                        )));
                    }
                }

                if ( isset( $_POST['restrict_age']['age_type'] ) && sanitize_key( wp_unslash( $_POST['restrict_age']['age_type'] ) ) === "input_age" ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- no nonce available for this AJAX handler

                    if ($output['ma_el_ra_day'] == "" || $output['ma_el_ra_month'] == "" || $output['ma_el_ra_year'] == "") {
                        die(json_encode(array(
                            "result" => "validate",
                            "output" => /* translators: %s: Restrict Age */ sprintf(__('%1$s &nbsp;&nbsp;', 'master-addons' ), sanitize_text_field( wp_unslash( isset( $_POST['restrict_age']['empty_bday'] ) ? $_POST['restrict_age']['empty_bday'] : '' ) )) // phpcs:ignore WordPress.Security.NonceVerification.Missing -- no nonce available for this AJAX handler
                        )));
                    } else if (!checkdate($output['ma_el_ra_month'], $output['ma_el_ra_day'], $output['ma_el_ra_year'])) {
                        die(json_encode(array(
                            "result" => "validate",
                            "output" => /* translators: %s: Restrict Age */ sprintf(__('%1$s &nbsp;&nbsp;', 'master-addons' ), sanitize_text_field( wp_unslash( isset( $_POST['restrict_age']['non_exist_bday'] ) ? $_POST['restrict_age']['non_exist_bday'] : '' ) )) // phpcs:ignore WordPress.Security.NonceVerification.Missing -- no nonce available for this AJAX handler
                        )));
                    } else {
                        $birthday = sprintf("%04d-%02d-%02d", $output['ma_el_ra_year'], $output['ma_el_ra_month'], $output['ma_el_ra_day']);
                        $today = new \DateTime();
                        $min = $today->modify("-{$min_age} year")->format("Y-m-d");

                        // Check if after the minimum age date
                        if ($birthday > $min) {
                            die(json_encode(array(
                                "result" => "validate",
                                "output" => /* translators: %s: Error message */ sprintf(__('%1$s , minimum age %2$s', 'master-addons' ), sanitize_text_field($min_age), wp_kses_post( wp_unslash( isset( $_POST['error_message'] ) ? $_POST['error_message'] : '' ) )) // phpcs:ignore WordPress.Security.NonceVerification.Missing -- no nonce available for this AJAX handler
                            )));
                        }
                    }
                }
            }

            die(json_encode(array(
                "result" => "success",
                "output" => ""
            )));
        } // end if fields

    }


    // Domain Checker Content
    public function jltma_domain_checker()
    {
        // Check security field first
        if (empty($_POST['nonce']) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'jltma-domain-checker')) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
            wp_send_json_error(__('Security Error.', 'master-addons'));
        }

        $succes_msg = isset($_POST['succes_msg']) ? urldecode(html_entity_decode(wp_kses_post( wp_unslash( $_POST['succes_msg'] ) ))) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
        $error_msg = isset($_POST['error_msg']) ? urldecode(html_entity_decode(wp_kses_post( wp_unslash( $_POST['error_msg'] ) ))) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
        $not_found = isset($_POST['not_found']) ? urldecode(html_entity_decode(wp_kses_post( wp_unslash( $_POST['not_found'] ) ))) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
        $not_entered_domain = isset($_POST['not_entered_domain']) ? sanitize_text_field( wp_unslash( $_POST['not_entered_domain'] ) ) : __('Please enter a domain name.', 'master-addons'); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above

        if (empty($_POST['domain'])) {
            wp_send_json_error($not_entered_domain);
        }

        $domain = str_replace(array('www.', 'http://'), '', sanitize_text_field( wp_unslash( $_POST['domain'] ) )); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above
        $split  = explode('.', $domain);
        if (count($split) == 1) {
            $domain = $domain . ".com";
        }
        $domain = preg_replace("/[^-a-zA-Z0-9.]+/", "", $domain);

        if (strlen($domain) > 0) {
            // Class responsible for checking if a domain is registered
            $domain_check = new Domain_Checker();
            $available    = $domain_check->is_available($domain);

            $domain_html = '<strong>' . esc_html($domain) . '</strong>';
            // Replace both %s and HTML entity variants
            $search = ['%s', '&percnt;s', '%25s'];

            switch ($available) {
                case '1':
                    wp_send_json_success(str_replace($search, $domain_html, $succes_msg));
                    break;

                case '0':
                    wp_send_json_error(str_replace($search, $domain_html, $error_msg));
                    break;

                default:
                    wp_send_json_error($not_found);
            }
        }

        wp_send_json_error(__('Please enter a valid Domain name.', 'master-addons' ));
    }
}

    // Ajax_Queries::get_instance();
